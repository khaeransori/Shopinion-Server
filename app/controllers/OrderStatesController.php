<?php

class OrderStatesController extends \BaseController {

	function __construct(OrderState $repo, REST $rest) {
		$this->repo = $repo;
		$this->rest = $rest;
	}

	/**
	 * Display a listing of the resource.
	 * GET /orderstates
	 *
	 * @return Response
	 */
	public function index()
	{
		$states = $this->repo->get();

		return $this->rest->response(200, $states);
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /orderstates
	 *
	 * @return Response
	 */
	public function store()
	{
		$state = new $this->repo;

		if ($state->save()) {
			return $this->rest->response(201, $state);
		}

		return $this->response->errorBadRequest($state->errors());
	}

	/**
	 * Display the specified resource.
	 * GET /orderstates/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$state = $this->repo->findOrFail($id);

		return $this->rest->response(201, $state);
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /orderstates/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$state = $this->repo->findOrFail($id);
		
		if ($state->save()) {
			return $this->rest->response(201, $state);
		}

		return $this->response->errorBadRequest($state->errors());
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /orderstates/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$state = $this->repo->findOrFail($id);
		
		if ($this->repo->destroy($id)) {
			return $this->rest->response(202, $state);
		}

		return $this->response->errorBadRequest();
	}

}