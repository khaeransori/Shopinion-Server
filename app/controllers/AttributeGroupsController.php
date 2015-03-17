<?php

class AttributeGroupsController extends \BaseController {

	function __construct(AttributeGroup $repo, REST $rest) {
		$this->repo = $repo;
		$this->rest = $rest;
	}
	/**
	 * Display a listing of attributegroups
	 *
	 * @return Response
	 */
	public function index()
	{
		return $this->rest->response(200, $this->repo->with('attributes')->get());
	}

	/**
	 * Store a newly created attributegroup in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$attributegroup = new $this->repo;

		if ($attributegroup->save())
		{
			return $this->rest->response(201, $attributegroup);
		}

		return $this->response->errorBadRequest($attributegroup->errors());
	}

	/**
	 * Display the specified attributegroup.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$attributegroup = $this->repo->with('attributes')->findOrFail($id);

		return $this->rest->response(200, $attributegroup);
	}

	/**
	 * Update the specified attributegroup in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$attributegroup = $this->repo->with('attributes')->findOrFail($id);

		if ($attributegroup->save())
		{
			return $this->rest->response(202, $attributegroup);
		}

		return $this->response->errorBadRequest($attributegroup->errors());
	}

	/**
	 * Remove the specified attributegroup from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$attributegroup = $this->repo->with('attributes')->findOrFail($id);

		if ($this->repo->destroy($id)) {
			return $this->rest->response(202, $attributegroup);
		}

		return $this->response->errorBadRequest();
	}

}
