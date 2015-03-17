<?php

class MobileManufacturersController extends \BaseController {

	function __construct(Manufacturer $repo, REST $rest) {
		$this->repo = $repo;
		$this->rest = $rest;
	}
	/**
	 * Display a listing of the resource.
	 * GET /mobile/manufacturers
	 *
	 * @return Response
	 */
	public function index()
	{
		$manufacturers = $this->repo
								->whereActive(1)
								->orderBy('name', 'ASC')
								->get();
								
		return $this->rest->response(200, $manufacturers);
	}

	/**
	 * Display the specified resource.
	 * GET /mobile/manufacturers/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$manufacturer = $this->repo->findOrFail($id);

		return $this->rest->response(200, $manufacturer);
	}

}