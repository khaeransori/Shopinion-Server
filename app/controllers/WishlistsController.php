<?php

use Dingo\Api\Routing\ControllerTrait;

class WishlistsController extends Controller {

	use ControllerTrait;

	function __construct(Customer $customer, Product $product) {
		$this->protect();
		$this->customer = $customer;
		$this->product  = $product;
	}

	/**
	 * Store a newly created wishlist in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		return API::user();
	}

	/**
	 * Display the specified wishlist.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$wishlist = Wishlist::findOrFail($id);

		return View::make('wishlists.show', compact('wishlist'));
	}

	/**
	 * Remove the specified wishlist from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Wishlist::destroy($id);

		return Redirect::route('wishlists.index');
	}

}
