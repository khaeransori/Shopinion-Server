<?php

use Dingo\Api\Routing\ControllerTrait;

class WishlistsController extends Controller {

	use ControllerTrait;

	function __construct(Customer $customer, Product $product, REST $rest) {
		$this->protect();

		$this->customer = $customer;
		$this->product  = $product;
		$this->rest     = $rest;
	}

	/**
	 * Store a newly created wishlist in storage.
	 *
	 * @return Response
	 */
	public function store()
	{

		$user = API::user();
		$user->load('customer');
		$is_exist = $this->customer->whereHas('wishlist', function ($query)
		{
			$query->where('product_id', Input::get('product_id'));
		})->count();

		if ((bool) $is_exist) {
			$query = $user->customer->wishlist()->detach(Input::get('product_id'));
		} else {
			$query = $user->customer->wishlist()->sync([Input::get('product_id')], false);
		}

		if ($query) {
			$user->load('customer.wishlist');
			
			$response = $user->customer->wishlist->count();
			return $this->rest->response(200, $response, false);
		}

		return $this->response->errorBadRequest();
	}

	/**
	 * Remove the specified wishlist from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$user = API::user();
		$user->load('customer');
		if ($user->customer->wishlist()->detach(Input::get('product_id'))) {
			
			$user->load('customer.wishlist');
			
			$response = $user->customer->wishlist->count();
			return $this->rest->response(200, $response, false);
		}

		return $this->response->errorBadRequest();
	}

}
