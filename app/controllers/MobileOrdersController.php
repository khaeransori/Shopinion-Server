<?php

use Dingo\Api\Routing\ControllerTrait;

class MobileOrdersController extends Controller {

	use ControllerTrait;

	function __construct(
		Order $repo, 
		OrderDetail $detail, 
		OrderHistory $history,
		OrderState $state,
		Customer $customer, 
		CustomerAddress $address, 
		Cart $cart, 
		Carrier $carrier, 
		Product $product, 
		ProductAttribute $product_attribute, 
		Payment $payment,
		REST $rest,
		Stock $stock,
		StockMovement $movement) {

		$this->protect();

		$this->repo              = $repo;
		$this->detail            = $detail;
		$this->product           = $product;
		$this->product_attribute = $product_attribute;
		$this->cart              = $cart;
		$this->customer          = $customer;
		$this->carrier           = $carrier;
		$this->payment           = $payment;
		$this->address           = $address;
		$this->state             = $state;
		$this->rest              = $rest;
		$this->stock             = $stock;
		$this->movement          = $movement;
		$this->history 			 = $history;
	}

	/**
	 * Display a listing of the resource.
	 * GET /mobileorders
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = API::user();
		$customer = $user->customer;

		$orders = $this->repo
						->with('state')
						->where('customer_id', $customer->id)
						->orderBy('created_at', 'DESC')
						->paginate($limit = 10);

		return $this->rest->response(200, $orders);
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /mobileorders
	 *
	 * @return Response
	 */
	public function store()
	{
		$user = API::user();
		$cart = $this->cart
					->where('customer_id', $user->customer->id)
					->where('is_customer', 1)
					->where('ordered', 0)
					->first();

		$carrier_id          = Input::get('carrier_id');
		$delivery_address_id = Input::get('delivery_address_id', 0);
		$invoice_address_id  = Input::get('invoice_address_id', 0);
		$message             = Input::get('message', '');
		$payment_id          = Input::get('payment_id', 0);

		$product_to_input = array();
		$warning          = array();
		$total_price      = 0;
		foreach ($cart->products as $product) {
			$p = $this->product->findOrFail($product['product_id']);
			$product_name  = $p->name;
			$product_price = (!($p->sale_price === '0.000000')) ? $p->sale_price : $p->price;

			if (!($product['product_attribute_id'] === '0')) {
				$combination = $this->product_attribute->findOrFail($product['product_attribute_id']);
				$combination->load('attribute_combinations', 'stock');

				$attribute_string = '';
				foreach ($combination->attribute_combinations as $attribute) {
					$attribute_string .= $attribute->group->name . ':' . $attribute->name . ', ';
				}

				$product_name .= ' [' . $attribute_string . ']';
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
			return $this->response->errorBadRequest($warning);
		}

		// # get state awal
		$carrier = $this->carrier->findOrFail($carrier_id);
		if ($carrier->on_store === 1) {
			$state         = $this->state
									->where('order', 2)
									->first();
		} else {
			$state         = $this->state
									->where('order', 1)
									->where('canceled', 0)
									->first();
		}
		$current_state = $state->id;

		// # validasi dulu sebelum di instansasi
		$data = array(
			'customer_id'         => $user->customer->id,
			'cart_id'             => $cart->id,
			'carrier_id'          => $carrier_id,
			'delivery_address_id' => $delivery_address_id,
			'invoice_address_id'  => $invoice_address_id,
			'current_state'       => $current_state,
			'message'             => $message,
			'payment_id'          => $payment_id,
			'total_product'       => $total_price,
			'shipping_price'      => 0
		);
		// return $arrayName = array('products' => $product_to_input );
		$validator = Validator::make($data, Order::$rules);

		// # instantiate Orders
		if ($validator->passes()) {

			DB::beginTransaction();

			try {
				$order    = $this->repo->create($data);
				$order_id = $order->id;

				foreach ($product_to_input as $product) {
					$product['order_id'] = $order_id;

					$this->detail->create($product);

					if (!($product['product_attribute_id'] === '0')) {
						$stock = $this->stock
										->where('product_attribute_id', $product['product_attribute_id'])
										->first();
					} else {
						$stock = $this->stock
										->where('product_id', $product['product_id'])
										->where('product_attribute_id', 0)
										->first();
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

				$cart->ordered = 1;
				$cart->save();

				# buat history awal
				$history = array(
					'order_id' => $order_id,
					'order_state_id' => $current_state
					);

				$this->history->create($history);
				
				// kosong, bikin cart baru
				$new_cart = new $this->cart();

				$new_cart->customer_id 			= $user->customer->id;
				$new_cart->carrier_id 			= 0;
				$new_cart->delivery_address_id 	= 0;
				$new_cart->invoice_address_id 	= 0;
				$new_cart->is_customer 			= 1;
				$new_cart->ordered 				= 0;
				
				$new_cart->save();

				$order['cart'] = array(
					'id' => $cart->id,
					'qty'=> 0
				);
				DB::commit();

				return $this->rest->response(201, $order);
			} catch (\Exception $e) {
				DB::rollBack();
				
				return $e->getMessage();
				// return $this->response->errorBadRequest($e->getMessage());

			}
			
		}

		return $this->response->errorBadRequest($validator->messages());
		
	}

	/**
	 * Display the specified resource.
	 * GET /mobileorders/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$order = $this->repo
						->with(
							'carrier',
							'detail.product.images',
							'delivery_address',
							'invoice_address',
							'state',
							'payment'
							)
						->findOrFail($id);

		return $this->rest->response(200, $order);
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /mobileorders/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /mobileorders/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /mobileorders/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}