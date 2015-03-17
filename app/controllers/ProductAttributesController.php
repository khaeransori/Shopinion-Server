<?php

class ProductAttributesController extends \BaseController {

	function __construct(ProductAttribute $repo, Product $product, Stock $stock, REST $rest) {
		$this->product = $product;
		$this->repo = $repo;
		$this->rest = $rest;
		$this->stock = $stock;
	}
	/**
	 * Display a listing of the resource.
	 * GET /productattributes
	 *
	 * @return Response
	 */
	public function index()
	{
		$product_id = Input::get('product_id');

		$this->product->findOrFail($product_id);

		// return $this->rest->response(200, $this->repo->with('attribute_combinations', 'attribute_combinations.group')->get());
		return $this->rest->response(200, $this->repo
			->with('attribute_combinations', 'attribute_combinations.group', 'stock')
			->where('product_id', $product_id)
			->get());
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /productattributes
	 *
	 * @return Response
	 */
	public function store()
	{
		$product_id = Input::get('product_id');
		$attributes = Input::get('attributes', 0);

		$this->product->findOrFail($product_id);

		if (!$attributes) {
			$errors = array(
				'message' => array(
					'attributes' => ['The attributes field is required.']
				)
			);

			return $this->response->errorBadRequest($errors);
		}

		$combination = new $this->repo;

		if ($combination->save()) {

			#create zero stock
			$stock = array(
				'product_id'           => $product_id,
				'product_attribute_id' => $combination->id,
				'qty'                  => 0
			);

			$this->stock->create($stock);

			if (Input::get('default_on')) {
				$this->repo
						->where('product_id', Input::get('product_id'))
						->update(array('default_on' => 0));

				$this->repo
						->where('id', $combination->id)
						->update(array('default_on' => 1));
			}

			$combination->attribute_combinations()->attach($attributes); #ini yang masih jadi PR, buat relasinya
			$combination->load('attribute_combinations', 'attribute_combinations.group', 'stock');
			return $this->rest->response(201, $combination);
		}

		return $this->response->errorBadRequest($combination->errors());
	}

	/**
	 * Display the specified resource.
	 * GET /productattributes/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$combination = $this->repo
							->with('attribute_combinations', 'attribute_combinations.group', 'stock')
							->findOrFail($id);

		return $this->rest->response(200, $combination);
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /productattributes/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$attributes = Input::get('attributes', 0);
		$default_on = Input::get('default_on', 0);
		$product_id = Input::get('product_id');

		$combination = $this->repo->findOrFail($id);
		$this->product->findOrFail($product_id);

		$old_default_on = $combination->default_on;

		if (!$attributes) {
			$errors = array(
				'message' => array(
					'attributes' => ['The attributes field is required.']
				)
			);

			return $this->response->errorBadRequest($errors);
		}

		if ($combination->save())
		{
			$combination->attribute_combinations()->sync($attributes);

			if ($default_on) {
				$this->repo
						->where('product_id', $combination->product_id)
						->update(array('default_on' => 0));

				$this->repo
						->where('id', $combination->id)
						->update(array('default_on' => 1));
			} else {
				if ($old_default_on) {
					$this->repo
							->where('product_id', $combination->product_id)
							->take(1)
							->update(array('default_on' => 1));
				}
			}

			$combination->load('attribute_combinations', 'attribute_combinations.group', 'stock');
			
			return $this->rest->response(202, $combination);
		}

		return $this->response->errorBadRequest($combination->errors());
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /productattributes/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$combination = $this->repo->findOrFail($id);

		if ($this->repo->destroy($id)) {
			if ($combination->default_on) {
				$this->repo
						->where('product_id', $combination->product_id)
						->take(1)
						->update(array('default_on' => 1));
			}

			return $this->rest->response(202, $combination);
		}

		return $this->response->errorBadRequest();
	}
}
