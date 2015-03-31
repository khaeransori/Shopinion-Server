<?php

class ProductsController extends \BaseController {

	function __construct(Product $repo, Stock $stock, REST $rest) {
		$this->repo = $repo;
		$this->rest = $rest;
		$this->stock = $stock;
	}
	/**
	 * Display a listing of the resource.
	 * GET /products
	 *
	 * @return Response
	 */
	public function index()
	{
		$search = Input::get('search', 0);
		$active = Input::get('active', 0);

		$products = $this->repo;

		if ($search == 1) {
			$term = Input::get('term');
			$products = $products->where(function ($query) use ($term)
			{
				$query->where('name', 'like', "%$term%")
						->orWhere('reference_code', 'like', "%$term%");
			});
		}

		if ($active == 1) {
			$products = $products->where('active', '=', 1);
		}

		$products = $products->with(
								array(
									'productStock',
									'aggregateStock',
									'categories',
									'category',
									'combinations.attribute_combinations.group',
									'combinations.stock',
									'features',
									'images'
								)
							)
							->get();
		return $this->rest->response(200, $products);
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /products
	 *
	 * @return Response
	 */
	public function store()
	{
		$active = Input::get('active', 0);

		$product = new $this->repo;

		if ($product->save()) {
			
			#create zero stock
			$stock = array(
				'product_id'           => $product->id,
				'product_attribute_id' => 0,
				'qty'                  => 0
			);

			$this->stock->create($stock);

			if (Input::has('categories') && is_array(Input::get('categories'))) {
				$product->categories()->sync(Input::get('categories'));
			}

			return $this->rest->response(201, $product);
		}

		return $this->response->errorBadRequest($product->errors());
	}

	/**
	 * Display the specified resource.
	 * GET /products/{id}
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

	/**
	 * Update the specified resource in storage.
	 * PUT /products/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$product = $this->repo->findOrFail($id);
		$toggle = Input::get('toggle', 0);

		if ($product->updateUniques())
		{
			if ($toggle !== 1) {
				if (Input::has('categories') && is_array(Input::get('categories'))) {
					$product->categories()->sync(Input::get('categories'));
				}

				if (Input::has('features') && is_array(Input::get('features'))) {
					$product->features()->sync(Input::get('features'));
				}
			}

			return $this->rest->response(202, $product);
		}

		return $this->response->errorBadRequest($product->errors());
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /products/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$product = $this->repo->findOrFail($id);

		if ($this->repo->destroy($id)) {
			return $this->rest->response(202, $product);
		}

		return $this->response->errorBadRequest();
	}

}