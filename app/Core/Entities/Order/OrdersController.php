<?php namespace App\Core\Entities\Order;

use App\Core\Criteria\IsCustomerCriteria;
use App\Core\Criteria\DeliveredCriteria;
use App\Core\Criteria\GetByCustomerIdCriteria;
use App\Core\Criteria\OrderedCriteria;
use App\Core\Entities\Carrier\CarrierRepository;
use App\Core\Entities\Cart\CartRepository;
use App\Core\Entities\Customer\CustomerRepository;
use App\Core\Entities\CustomerAddress\CustomerAddressRepository;
use App\Core\Entities\Order\OrderRepository;
use App\Core\Entities\OrderDetail\OrderDetailRepository;
use App\Core\Entities\OrderHistory\OrderHistoryRepository;
use App\Core\Entities\OrderState\OrderStateRepository;
use App\Core\Entities\Payment\PaymentRepository;
use App\Core\Entities\Product\ProductRepository;
use App\Core\Entities\ProductAttribute\ProductAttributeRepository;
use App\Core\Entities\Stock\StockRepository;
use App\Core\Entities\StockMovement\StockMovementRepository;
use Dingo\Api\Routing\ControllerTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Factory;

class OrdersController extends \Controller {

	use ControllerTrait;

	protected $address;
	protected $carrier;
	protected $cart;
	protected $customer;
	protected $detail;
	protected $history;
	protected $movement;
	protected $payment;
	protected $product;
	protected $product_attribute;
	protected $repository;
	protected $state;
	protected $stock;
	protected $validator;

	function __construct(
		CarrierRepository $carrier, 
		CartRepository $cart, 
		CustomerRepository $customer, 
		CustomerAddressRepository $address, 
		Factory $validator,
		OrderRepository $repository, 
		OrderDetailRepository $detail, 
		OrderHistoryRepository $history,
		OrderStateRepository $state,
		PaymentRepository $payment,
		ProductRepository $product, 
		ProductAttributeRepository $product_attribute, 
		StockRepository $stock,
		StockMovementRepository $movement
	) {

		$this->address           = $address;
		$this->carrier           = $carrier;
		$this->cart              = $cart;
		$this->customer          = $customer;
		$this->detail            = $detail;
		$this->history 			 = $history;
		$this->movement          = $movement;
		$this->payment           = $payment;
		$this->product           = $product;
		$this->product_attribute = $product_attribute;
		$this->repository        = $repository;
		$this->state             = $state;
		$this->stock             = $stock;
		$this->validator 		 = $validator;
	}
	/**
	 * Display a listing of orders
	 *
	 * @return Response
	 */
	public function index()
	{
		try {
			$delivered = \Input::get('delivered', 0);
			$limit = \Input::get('limit', false);
			
			if (!($delivered === 0)) {
				$this->repository->pushCriteria(new DeliveredCriteria());
			}

			$response = $this->repository->with([
				'customer',
				'cart',
				'carrier',
				'detail',
				'delivery_address',
				'history.state',
				'invoice_address',
				'state',
				'payment'
			]);

			if (!($limit === false) && is_numeric($limit)) {
				$response = $response->paginate($limit);
			} else {
				$response = $response->all();
			}

			return $this->response->array($response->toArray());
		} catch (\Exception $e) {
			throw new \Dingo\Api\Exception\ResourceException("Error Processing Request", $e->getMessage());
		}
	}

	/**
	 * Store a newly created order in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$cart_id             = \Input::get('cart_id');
		$cart     			 = $this->cart->find($cart_id);
		
		$customer_id         = $cart->customer_id;
		$carrier_id          = $cart->carrier_id;
		$delivery_address_id = $cart->delivery_address_id;
		$invoice_address_id  = $cart->invoice_address_id;

		$shipping_price      = \Input::get('shipping_price', 0);
		$payment_id          = \Input::get('payment_id');
		$message 			 = \Input::get('message', '');

		$customer = $this->customer->find($customer_id);
		$carrier  = $this->carrier->find($carrier_id);
		$payment  = $this->payment->find($payment_id);

		if ((int) $carrier->on_store === (int) 1) {
			$delivery_address_id = "";
			$invoice_address_id  = "";
		} else {
			$delivery_address = $this->address->find($delivery_address_id);
			$invoice_address  = $this->address->find($invoice_address_id);
		}

		$product_to_input = array();
		$warning          = array();
		$total_price      = 0;

		foreach ($cart->products as $product) {
			$p = $this->product->find($product['product_id']);

			$product_name  = $p->name;
			$product_price = ($p->sale_price === '0.000000') ? $p->price : $p->sale_price;

			if (!($product['product_attribute_id'] === '0')) {
				$combination = $this->product_attribute->getModel()->with('attribute_combinations', 'stock')->findOrFail($product['product_attribute_id']);
				$attribute_string = '';
				foreach ($combination->attribute_combinations as $attribute) {
					$attribute_string .= $attribute->group->name . ':' . $attribute->name . ', ';
				}

				$product_name .= ' [' . substr($attribute_string, 0, -2) . ']';
				if ($combination->stock->qty < $product['qty']) {
					$warning[] = array(
						'product_id'           => $product['product_id'],
						'product_attribute_id' => $product['product_attribute_id'],
						'product_name'         => $product_name,
						'qty'                  => $combination->stock->qty
					);
				}
			} else {
				if ($p->product_stock->qty < $product['qty']) {
					$warning[] = array(
						'product_id'           => $product['product_id'],
						'product_name'         => $product_name,
						'qty'                  => $p->product_stock->qty
					);
				}
			}

			$product_to_input[] = array(
				'product_id'             => $product['product_id'],
				'product_attribute_id'   => $product['product_attribute_id'],
				'product_name'           => $product_name,
				'product_reference'      => $p->reference_code,
				'product_quantity'       => $product['qty'],
				'product_price'          => $product_price,
				'total_price'            => ($product['qty'] * $product_price),
				'original_product_price' => $p->price
			);

			$total_price   = $total_price + ($product['qty'] * $product_price);
		}

		if (count($warning) > 0) {
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $warning);
		}

		// # get state awal
		$state         = $this->state->findByField('order', 2);
		$current_state = $state->id;

		// # validasi dulu sebelum di instansasi
		$data = array(
			'customer_id'         => $customer_id,
			'cart_id'             => $cart_id,
			'carrier_id'          => $carrier_id,
			'delivery_address_id' => $delivery_address_id,
			'invoice_address_id'  => $invoice_address_id,
			'current_state'       => $current_state,
			'message'             => $message,
			'payment_id'          => $payment_id,
			'total_product'       => $total_price,
			'shipping_price'      => $shipping_price
		);

		$validator = $this->validator->make($data, $this->repository->getModel()->getRules());
		// # instantiate Orders
		if ($validator->passes()) {

			DB::beginTransaction();

			try {
				$order    = $this->repository->create($data);
				$order_id = $order->id;

				foreach ($product_to_input as $product) {
					$product['order_id'] = $order_id;

					$this->detail->create($product);

					if ($product['product_attribute_id'] === '0') {
						$stock = $this->stock->getModel()
										->where('product_id', $product['product_id'])
										->where('product_attribute_id', 0)
										->first();
					} else {
						$stock = $this->stock->findByField('product_attribute_id', $product['product_attribute_id']);
					}

					$stock->qty = ($stock->qty - $product['product_quantity']);

					$stock->save();

					#buat movement stock
					$movement = array(
						'stock_id'                 => $stock->id,
						'stock_movement_reason_id' => 3,
						'qty'                      => $product['product_quantity']
						 );
					$this->movement->create($movement);
				}

				$this->cart->update(['ordered' => 1], $cart->id);

				# buat history awal
				$history = array(
					'order_id' => $order_id,
					'order_state_id' => $current_state
					);

				$this->history->create($history);
				
				DB::commit();

				return $this->response->array($order->toArray());
			} catch (\Exception $e) {
				DB::rollBack();
				
				throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getMessage());

			}
			
		}

		throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $validator->messages());
	}

	/**
	 * Display the specified order.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		try {
			$repository = $this->repository
							->with([
								'customer.user',
								'cart',
								'carrier',
								'detail.product.images',
								'delivery_address',
								'history.state',
								'invoice_address',
								'state',
								'payment',
								'payment_confirmation.bank'
							])
							->find($id);
			return $this->response->array($repository->toArray());
		} catch (\Exception $e) {
			throw new \Dingo\Api\Exception\ResourceException("Error Processing Request", $e->getMessage());
		}
	}

	/**
	 * Update the specified order in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		try {
			$repository  = $this->repository->with([
					'customer.user',
					'cart',
					'carrier',
					'detail.product.images',
					'delivery_address',
					'history.state',
					'invoice_address',
					'state',
					'payment'
				])->find($id);
			$add_history = FALSE;

			$shipping_price      = \Input::get('shipping_price', 0);
			$tracking_number     = \Input::get('tracking_number');
			$current_state_order = \Input::get('current_state_order');

			// $this->repository->getModel()->validate();

			# get state awal
			$state         = $this->state->findByField('order', $current_state_order);
			$current_state = $state->id;

			// throw new Exception("Error Processing Request", $current_state);
			
			if ((int)$state->paid === (int)1) {
				$data_to_update['paid'] = 1;
			}

			if (((int)$repository->paid === (int)1) && ((int)$state->delivered === (int)1)) {
				$data_to_update['delivered'] = 1;
			}

			if (!($current_state === $repository->current_state)) {
				$add_history = TRUE;
			}

			$data_to_update['shipping_price']  = $shipping_price;
			$data_to_update['tracking_number'] = $tracking_number;
			$data_to_update['current_state']   = $current_state;

			$this->repository->update($data_to_update, $id);

			if (TRUE === $add_history) {
				$history = array(
					'order_id' => $id,
					'order_state_id' => $current_state
					);

				$this->history->create($history);
			}
			
			$repository  = $this->repository->with([
					'customer.user',
					'cart',
					'carrier',
					'detail.product.images',
					'delivery_address',
					'history.state',
					'invoice_address',
					'state',
					'payment'
				])->find($id);
			return $this->response->array($repository->toArray());
		} catch (\LaravelBook\Ardent\InvalidModelException $e) {
			throw new \Dingo\Api\Exception\UpdateResourceFailedException("Error Processing Request", $e->getErrors());
		} catch (\Exception $e) {
			throw new \Dingo\Api\Exception\UpdateResourceFailedException("Error Processing Request", $e->getMessage());
		}
	}

	/////////////////////////////
	public function getCustomer()
	{
		$limit = \Input::get('limit', false);

		try {
	        if ($user = \JWTAuth::parseToken()->authenticate()) {

	        	$customer 	= $this->customer->findByField('user_id', $user->id);
	        	
	        	$this->repository->pushCriteria(new GetByCustomerIdCriteria($customer->id));

				$response = $this->repository->with(['state']);

				if (!($limit === false) && is_numeric($limit)) {
					$response = $response->paginate($limit);
				} else {
					$response = $response->all();
				}

				return $this->response->array($response->toArray());
	        }
	    } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
			throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    } catch (\Exception $e) {
	    	throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    }
	}
	public function storeCustomer()
	{
		try {
	        if ($user = \JWTAuth::parseToken()->authenticate()) {
	        	$customer 	= $this->customer->findByField('user_id', $user->id);

				$this->cart->pushCriteria(new OrderedCriteria(0));
				$this->cart->pushCriteria(new IsCustomerCriteria(1));

				$cart     			 = $this->cart->findByField('customer_id', $customer->id);

				$customer_id         = $cart->customer_id;
				$carrier_id          = \Input::get('carrier_id', 0);
				$delivery_address_id = \Input::get('delivery_address_id', 0);
				$invoice_address_id  = \Input::get('invoice_address_id', 0);

				$shipping_price      = \Input::get('shipping_price', 0);
				$payment_id          = \Input::get('payment_id');
				$message 			 = \Input::get('message', '');

				$customer = $this->customer->find($customer_id);
				$carrier  = $this->carrier->find($carrier_id);
				$payment  = $this->payment->find($payment_id);

				if ((int) $carrier->on_store === (int) 1) {
					$delivery_address_id = "";
					$invoice_address_id  = "";
				} else {
					$delivery_address = $this->address->find($delivery_address_id);
					$invoice_address  = $this->address->find($invoice_address_id);
				}

				$product_to_input = array();
				$warning          = array();
				$total_price      = 0;

				foreach ($cart->products as $product) {
					$p = $this->product->find($product['product_id']);

					$product_name  = $p->name;
					$product_price = ($p->sale_price === '0.000000') ? $p->price : $p->sale_price;

					if (!($product['product_attribute_id'] === '0')) {
						$combination = $this->product_attribute->getModel()->with('attribute_combinations', 'stock')->findOrFail($product['product_attribute_id']);
						$attribute_string = '';
						foreach ($combination->attribute_combinations as $attribute) {
							$attribute_string .= $attribute->group->name . ':' . $attribute->name . ', ';
						}

						$product_name .= ' [' . substr($attribute_string, 0, -2) . ']';
						if ($combination->stock->qty < $product['qty']) {
							$warning[] = array(
								'product_id'           => $product['product_id'],
								'product_attribute_id' => $product['product_attribute_id'],
								'product_name'         => $product_name,
								'qty'                  => $combination->stock->qty
							);
						}
					} else {
						if ($p->product_stock->qty < $product['qty']) {
							$warning[] = array(
								'product_id'           => $product['product_id'],
								'product_name'         => $product_name,
								'qty'                  => $p->product_stock->qty
							);
						}
					}

					$product_to_input[] = array(
						'product_id'             => $product['product_id'],
						'product_attribute_id'   => $product['product_attribute_id'],
						'product_name'           => $product_name,
						'product_reference'      => $p->reference_code,
						'product_quantity'       => $product['qty'],
						'product_price'          => $product_price,
						'total_price'            => ($product['qty'] * $product_price),
						'original_product_price' => $p->price
					);

					$total_price   = $total_price + ($product['qty'] * $product_price);
				}

				if (count($warning) > 0) {
					throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $warning);
				}

				// # get state awal
				$state         = $this->state->findByField('order', 2);
				$current_state = $state->id;

				// # validasi dulu sebelum di instansasi
				$data = array(
					'customer_id'         => $customer_id,
					'cart_id'             => $cart->id,
					'carrier_id'          => $carrier_id,
					'delivery_address_id' => $delivery_address_id,
					'invoice_address_id'  => $invoice_address_id,
					'current_state'       => $current_state,
					'message'             => $message,
					'payment_id'          => $payment_id,
					'total_product'       => $total_price,
					'shipping_price'      => $shipping_price
				);

				$validator = $this->validator->make($data, $this->repository->getModel()->getRules());
				// # instantiate Orders
				if ($validator->passes()) {

					DB::beginTransaction();

					try {
						$order    = $this->repository->create($data);
						$order_id = $order->id;

						foreach ($product_to_input as $product) {
							$product['order_id'] = $order_id;

							$this->detail->create($product);

							if ($product['product_attribute_id'] === '0') {
								$stock = $this->stock->getModel()
												->where('product_id', $product['product_id'])
												->where('product_attribute_id', 0)
												->first();
							} else {
								$stock = $this->stock->findByField('product_attribute_id', $product['product_attribute_id']);
							}

							$stock->qty = ($stock->qty - $product['product_quantity']);

							$stock->save();

							#buat movement stock
							$movement = array(
								'stock_id'                 => $stock->id,
								'stock_movement_reason_id' => 3,
								'qty'                      => $product['product_quantity']
								 );
							$this->movement->create($movement);
						}

						$this->cart->scopeReset()->update(['ordered' => 1], $cart->id);

						# buat history awal
						$history = array(
							'order_id' => $order_id,
							'order_state_id' => $current_state
							);

						$this->history->create($history);
						
						DB::commit();

						return $this->response->array($order->toArray());
					} catch (\Exception $e) {
						DB::rollBack();
						
						throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getMessage());

					}
					
				}

				throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $validator->messages());
	        	
	        }
	    } catch (\Dingo\Api\Exception\StoreResourceFailedException $e) {
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getErrors());
	    } catch (\LaravelBook\Ardent\InvalidModelException $e) {
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getErrors());
	    } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
			throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    } catch (\Exception $e) {
	    	\DB::rollBack();
			throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    }
	}
}
