<?php

class StockMovementReasonsController extends \BaseController {

	function __construct(StockMovementReason $repo, REST $rest) {
		$this->repo = $repo;
		$this->rest = $rest;
	}

	/**
	 * Display a listing of the resource.
	 * GET /stockmovementreasons
	 *
	 * @return Response
	 */
	public function index()
	{
		$sign = Input::get('sign', 1);
		return $this->rest->response(200, $this->repo->where('sign', $sign)->get(array('id', 'sign', 'name')));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /stockmovementreason/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /stockmovementreason
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /stockmovementreason/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /stockmovementreason/{id}/edit
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
	 * PUT /stockmovementreason/{id}
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
	 * DELETE /stockmovementreason/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}