<?php

class ManufacturersController extends \BaseController {

	function __construct(Manufacturer $repo, REST $rest) {
		$this->repo = $repo;
		$this->rest = $rest;
	}
	/**
	 * Display a listing of the resource.
	 * GET /manufacturers
	 *
	 * @return Response
	 */
	public function index()
	{
		$manufacturers = $this->repo
								->with('products')
								->get();
								
		return $this->rest->response(200, $manufacturers);
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /manufacturers
	 *
	 * @return Response
	 */
	public function store()
	{
		$manufacturer = new $this->repo;

		if ($manufacturer->save()) {
			return $this->rest->response(201, $manufacturer);
		}

		return $this->response->errorBadRequest($manufacturer->errors());
	}

	/**
	 * Display the specified resource.
	 * GET /manufacturers/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$manufacturer = $this->repo->findOrFail($id);

		return $this->rest->response(200, $manufacturer);
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /manufacturers/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$manufacturer = $this->repo->findOrFail($id);

		if ($manufacturer->save())
		{
			return $this->rest->response(202, $manufacturer);
		}

		return $this->response->errorBadRequest($manufacturer->errors());
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /manufacturers/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$manufacturer = $this->repo->findOrFail($id);

		if ($this->repo->destroy($id)) {
			return $this->rest->response(202, $manufacturer);
		}

		return $this->response->errorBadRequest();
	}

}