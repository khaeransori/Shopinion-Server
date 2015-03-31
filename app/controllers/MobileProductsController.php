<?php

class MobileProductsController extends \BaseController {

	function __construct(Product $repo, Stock $stock, REST $rest) {
		$this->repo = $repo;
		$this->rest = $rest;
		$this->stock = $stock;
	}
	/**
	 * Display a listing of the resource.
	 * GET /mobile/products
	 *
	 * @return Response
	 */
	public function index()
	{
		$sale         = Input::get('sale', 0);
		$manufacturer = Input::get('manufacturer', 0);
		$category     = Input::get('category', 0);
		$is_wishlist  = Input::get('is_wishlist', 0);
		$products = $this->repo
							->with('images')
							->whereActive(1);

		if ($sale !== 0) {
			$products = $products->where('sale_price', '!=', "0.000000");
		}

		if ($manufacturer !== 0) {
			$products = $products->where('manufacturer_id', $manufacturer);
		}

		if ($category !== 0) {
			$products = $products->whereHas('category', function ($query)
			{
				$query->where('id', Input::get('category'));
			});
		}

		if ($is_wishlist !== 0) {
			$user = API::user();
			$user->load('customer');

			$customer_id = $user->customer->id;
			$products = $products->whereHas('wishlist', function ($query) use ($customer_id)
			{
				$query->where('customer_id', $customer_id);
			});
		}

		$products = $products->orderBy('created_at', 'DESC')
					->paginate($limit = 10);

		return $this->rest->response(200, $products, false);
	}

	/**
	 * Display the specified resource.
	 * GET /mobile/products/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{

		$product = $this->repo
							->with(
								array(
									'productStock',
									'aggregateStock',
									'categories',
									'category',
									'combinations',
									'features.feature',
									'images',
									'manufacturer'
								)
							)
							->findOrFail($id);

		$product['is_wishlist'] = 0;
		if (API::user()) {
			$is_exist = $this->repo->whereHas('wishlist', function ($query) use ($id)
			{
				$query->where('product_id', $id);
			})->count();

			$product['is_wishlist'] = $is_exist;
		}
		return $this->rest->response(200, $product);
	}
}