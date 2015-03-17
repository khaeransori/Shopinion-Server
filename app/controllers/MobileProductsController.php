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

		$products = $this->repo
							->with('images')
							->whereActive(1);

		if ($sale !== 0) {
			$products = $products->where('sale_price', '!=', "0.000000");
		}

		if ($manufacturer !== 0) {
			$products = $products->whereManufacturerId($manufacturer);
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
									'images'
								)
							)
							->findOrFail($id);

		return $this->rest->response(200, $product);
	}
}