<?php

class OrdersController extends \BaseController {

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
	 * Display a listing of orders
	 *
	 * @return Response
	 */
	public function index()
	{
		$delivered = Input::get('delivered', 0);
		$orders = $this->repo
						->with(
							'customer',
							'cart',
							'carrier',
							'detail',
							'delivery_address',
							'history.state',
							'invoice_address',
							'state',
							'payment'
							)
						->where('delivered', $delivered)
						->get();

		return $this->rest->response(200, $orders);
	}

	/**
	 * Store a newly created order in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$customer_id         = Input::get('customer_id');
		$cart_id             = Input::get('cart_id');
		$carrier_id          = Input::get('carrier_id');
		$shipping_price      = Input::get('shipping_price', 0);
		$delivery_address_id = Input::get('delivery_address_id', '');
		$invoice_address_id  = Input::get('invoice_address_id', '');
		$products            = Input::get('products');
		$payment_id          = Input::get('payment_id');
		$total_product       = Input::get('total_product');
		$message 			 = Input::get('message', '');

		$customer = $this->customer->findOrFail($customer_id);
		$cart     = $this->cart->findOrFail($cart_id);
		$carrier  = $this->carrier->findOrFail($carrier_id);
		$payment  = $this->payment->findOrFail($payment_id);

		if ($carrier->on_store !== 1) {
			$delivery_address = $this->address->findOrFail($delivery_address_id);
			$invoice_address  = $this->address->findOrFail($invoice_address_id);
		} else {
			$delivery_address_id = "";
			$invoice_address_id  = "";
		}

		$product_to_input = array();
		$warning          = array();
		$total_price      = 0;

		foreach ($products as $product) {
			$p = $this->product->findOrFail($product['product_id']);

			$product_name  = $p->name;
			$product_price = ($p->sale_price !== '0.000000') ? $p->sale_price : $p->price;

			if ($product['product_attribute_id'] !== "") {
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
		$state         = $this->state->whereOrder(2)->get()->first();
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

					if ($product['product_attribute_id'] !== "") {
						$stock = $this->stock
										->where('product_attribute_id', $product['product_attribute_id'])
										->get()->first();
					} else {
						$stock = $this->stock
										->where('product_id', $product['product_id'])
										->where('product_attribute_id', 0)
										->get()->first();
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
				
				DB::commit();

				return $this->rest->response(201, $order);
			} catch (\Exception $e) {
				DB::rollBack();
				
				return $this->response->errorBadRequest($e->getMessage());

			}
			
		}

		return $this->response->errorBadRequest($validator->messages());
	}

	/**
	 * Display the specified order.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$order = $this->repo
						->with(
							'customer.user',
							'cart',
							'carrier',
							'detail.product.images',
							'delivery_address',
							'history.state',
							'invoice_address',
							'state',
							'payment'
							)
						->findOrFail($id);

		return $this->rest->response(200, $order);
	}

	/**
	 * Update the specified order in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$order = $this->repo->findOrFail($id);
		$add_history = FALSE;

		$shipping_price      = Input::get('shipping_price', 0);
		$tracking_number     = Input::get('tracking_number');
		$current_state_order = Input::get('current_state_order');

		// # get state awal
		$state         = $this->state->whereOrder($current_state_order)->get()->first();
		$current_state = $state->id;

		if ($state->paid === 1) {
			$order->paid = 1;
		}

		if (($order->paid === 1) && ($state->delivered === 1)) {
			$order->delivered = 1;
		}

		if ($current_state !== $order->current_state) {
			$add_history = TRUE;
		}
		$order->shipping_price  = $shipping_price;
		$order->tracking_number = $tracking_number;
		$order->current_state   = $current_state;

		if ($order->save()) {
			if (TRUE === $add_history) {
				$history = array(
					'order_id' => $id,
					'order_state_id' => $current_state
					);

				$this->history->create($history);
			}

			$order->load(
							'customer.user',
							'cart',
							'carrier',
							'detail.product.images',
							'delivery_address',
							'history.state',
							'invoice_address',
							'state',
							'payment'
							);

			return $this->rest->response(201, $order);
		}

		return $this->response->errorBadRequest();
	}

	/**
	 * Remove the specified order from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Order::destroy($id);

		return Redirect::route('orders.index');
	}

}
