<?php

class FeatureValuesController extends \BaseController {

	function __construct(FeatureValue $repo, REST $rest) {
		$this->repo = $repo;
		$this->rest = $rest;
	}

	/**
	 * Display a listing of the resource.
	 * GET /featurevalues
	 *
	 * @return Response
	 */
	public function index()
	{
		return $this->rest->response(200, $this->repo->get());
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /featurevalues
	 *
	 * @return Response
	 */
	public function store()
	{
		$featurevalue = new $this->repo;

		if ($featurevalue->save()) {
			return $this->rest->response(201, $featurevalue);
		}

		return $this->response->errorBadRequest($featurevalue->errors());
	}

	/**
	 * Display the specified resource.
	 * GET /featurevalues/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$featurevalue = $this->repo->findOrFail($id);

		return $this->rest->response(200, $featurevalue);
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /featurevalues/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$featurevalue = $this->repo->findOrFail($id);

		if ($featurevalue->save()) {
			return $this->rest->response(202, $featurevalue);
		}

		return $this->response->errorBadRequest($featurevalue->errors());
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /featurevalues/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$featurevalue = $this->repo->findOrFail($id);

		if ($this->repo->destroy($id)) {
			return $this->rest->response(202, $featurevalue);
		}

		return $this->response->errorBadRequest();
	}

}