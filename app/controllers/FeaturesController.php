<?php

class FeaturesController extends \BaseController {

	function __construct(Feature $repo, REST $rest) {
		$this->repo = $repo;
		$this->rest = $rest;
	}
	
	/**
	 * Display a listing of the resource.
	 * GET /features
	 *
	 * @return Response
	 */
	public function index()
	{
		return $this->rest->response(200, $this->repo->with('values')->get());
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$feature = new $this->repo;

		if ($feature->save()) {
			return $this->rest->response(201, $feature);
		}

		return $this->response->errorBadRequest($feature->errors());
	}

	/**
	 * Display the specified resource.
	 * GET /features/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$feature = $this->repo->with('values')->findOrFail($id);

		return $this->rest->response(200, $feature);
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /features/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$feature = $this->repo->with('values')->findOrFail($id);

		if ($feature->save()) {
			return $this->rest->response(202, $feature);
		}

		return $this->response->errorBadRequest($feature->errors());
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /features/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$feature = $this->repo->with('values')->findOrFail($id);

		if ($this->repo->destroy($id)) {
			return $this->rest->response(202, $feature);
		}

		return $this->response->errorBadRequest();
	}

}