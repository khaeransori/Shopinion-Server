<?php

class StockMovementsController extends \BaseController {

	function __construct(Stock $repo, StockMovement $movement, StockMovementReason $reason, Product $product, ProductAttribute $product_attribute, REST $rest) {
		$this->movement 			= $movement;
		$this->product 				= $product;
		$this->product_attribute 	= $product_attribute;
		$this->reason 				= $reason;
		$this->repo 				= $repo;
		$this->rest 				= $rest;
	}

	/**
	 * Display a listing of stocks
	 *
	 * @return Response
	 */
	public function index()
	{
		$response = $this->movement
							->withTrashed()
							->with(
								[
									'stock.product' => function ($query)
														{
															$query->withTrashed();
														},
									'stock.combination.attribute_combinations.group',
									'reason'
								]
							)
							// ->orderBy('created_at', 'desc')
							->get();
		return $this->rest->response(200, $response);
	}

	/**
	 * Store a newly created stock in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$stock_movement_reason_id 	= Input::get('stock_movement_reason_id');
		$product_id 				= Input::get('product_id');
		$product_attribute_id 		= Input::get('product_attribute_id', 0);
		$qty 						= Input::get('qty');

		$message = array(
				'qty' => array(
					'message' => ['Available stock is not enough to decreased']
				)
			);

		$stock = new $this->repo;
		$stock->qty = 0;

		$this->product->findOrFail($product_id);
		$reason = $this->reason->findOrFail($stock_movement_reason_id);

		if (Input::has('product_attribute_id')) {
			$this->product_attribute->findOrFail($product_attribute_id);
			
			if ($this->repo->where('product_attribute_id', $product_attribute_id)->count()) {
				$stock = $this->repo->where('product_attribute_id', $product_attribute_id)->get()->first();
			}
		} else {

			if ($this->repo->where('product_id', $product_id)->where('product_attribute_id', 0)->count()) {
				$stock = $this->repo->where('product_id', $product_id)->where('product_attribute_id', 0)->get()->first();
			}

		}

		$qty_to_add = ($reason->sign * $qty);

		if (($reason->sign < 0) && ($stock->qty < $qty)) {
			return $this->response->errorBadRequest($message);
		}

		// $stock->stock_movement_reason_id 	= $stock_movement_reason_id;
		$stock->product_id 					= $product_id;
		$stock->product_attribute_id 		= $product_attribute_id;
		$stock->qty 						= $stock->qty + $qty_to_add;

		$validator = Validator::make(Input::all(), Stock::$rules);

		if ($validator->passes()) {

			$validator = Validator::make(Input::all(), StockMovement::$rules);

			if ($stock->save()) {
				
				$movement 			= new $this->movement;
				$movement->stock_id = $stock->id;
				$movement->qty      = $qty;
				$movement->stock_movement_reason_id = $stock_movement_reason_id;
				$movement->save();

				return $this->rest->response(201, $stock);
			}

			return $this->response->errorBadRequest($stock->errors());
			
		}

		return $this->response->errorBadRequest($validator->errors());
	}

	/**
	 * Display the specified stock.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$stock = Stock::findOrFail($id);

		return View::make('stocks.show', compact('stock'));
	}

	/**
	 * Show the form for editing the specified stock.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$stock = Stock::find($id);

		return View::make('stocks.edit', compact('stock'));
	}

	/**
	 * Update the specified stock in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$stock = Stock::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Stock::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$stock->update($data);

		return Redirect::route('stocks.index');
	}

	/**
	 * Remove the specified stock from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Stock::destroy($id);

		return Redirect::route('stocks.index');
	}

}
