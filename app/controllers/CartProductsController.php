<?php

class CartProductsController extends \BaseController {

	function __construct(CartProduct $repo, REST $rest, Cart $cart, Product $product, ProductAttribute $product_attribute) {
		$this->repo = $repo;
		$this->rest = $rest;
		$this->cart = $cart;
		$this->product = $product;
		$this->product_attribute = $product_attribute;
	}

	/**
	 * Display a listing of cartproducts
	 *
	 * @return Response
	 */
	public function index()
	{
		return $this->rest->response(200, $this->repo->with('product.images',  'combination.attribute_combinations.group')->get());
	}

	/**
	 * Store a newly created cartproduct in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$cart_id 				= Input::get('cart_id');
		$product_id 			= Input::get('product_id');
		$product_attribute_id 	= Input::get('product_attribute_id', 0);
		$qty 					= Input::get('qty', 0);

		$message = array(
				'qty' => array(
					'message' => ['Available stock is not enough to add to cart']
				)
			);

		$this->cart->findOrFail($cart_id);
		$product = $this->product->with('productStock')->findOrFail($product_id);

		if ($product_attribute_id !== 0) {
			$product_attribute = $this->product_attribute->with('stock')->findOrFail($product_attribute_id);

			if ($product_attribute->stock->qty < $qty) {

				return $this->response->errorBadRequest($message);

			}
		} else {
			if ($product->product_stock->qty < $qty) {

				return $this->response->errorBadRequest($message);

			}
		}

		$exists = $this->repo
						->whereCartId($cart_id)
						->whereProductId($product_id);
		
		if ($product_attribute_id !== 0) {
			$exists = $exists->whereProductAttributeId($product_attribute_id);
		}

		$exists = $exists->get()->first();

		if ($exists) {
			$cart_product = $exists;
		} else {
			$cart_product = new $this->repo;
		}

		if ($cart_product->save())
		{
			$cart_product->load('product.images',  'combination.attribute_combinations.group');
			return $this->rest->response(201, $cart_product);
		}

		return $this->response->errorBadRequest($cart_product->errors());
	}

	/**
	 * Display the specified cartproduct.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$cartproduct = Cartproduct::findOrFail($id);

		return View::make('cartproducts.show', compact('cartproduct'));
	}

	/**
	 * Update the specified cartproduct in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$cartproduct = Cartproduct::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Cartproduct::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$cartproduct->update($data);

		return Redirect::route('cartproducts.index');
	}

	/**
	 * Remove the specified cartproduct from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$cartproduct = $this->repo
						->with('product.images',  'combination.attribute_combinations.group')
						->findOrFail($id);
						
		if ($this->repo->destroy($id)) {
			return $this->rest->response(202, $cartproduct);
		}

		return $this->response->errorBadRequest();
	}

}
