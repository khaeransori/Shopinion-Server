<?php

class CarriersController extends \BaseController {

	function __construct(Carrier $repo, REST $rest) {
		$this->repo = $repo;
		$this->rest = $rest;
	}

	/**
	 * Display a listing of the resource.
	 * GET /carriers
	 *
	 * @return Response
	 */
	public function index()
	{
		$carriers = $this->repo->get();

		return $this->rest->response(200, $carriers);
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /carriers
	 *
	 * @return Response
	 */
	public function store()
	{
		$carrier = new $this->repo;

		if ($carrier->save()) {
			return $this->rest->response(201, $carrier);
		}

		return $this->response->errorBadRequest($carrier->errors());
	}

	/**
	 * Display the specified resource.
	 * GET /carriers/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$carrier = $this->repo->findOrFail($id);

		return $this->rest->response(201, $carrier);
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /carriers/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$carrier = $this->repo->findOrFail($id);
		
		if ($carrier->save()) {
			return $this->rest->response(201, $carrier);
		}

		return $this->response->errorBadRequest($carrier->errors());
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /carriers/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$carrier = $this->repo->findOrFail($id);
		
		if ($this->repo->destroy($id)) {
			return $this->rest->response(202, $carrier);
		}

		return $this->response->errorBadRequest();
	}

}