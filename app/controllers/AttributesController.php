<?php

class AttributesController extends \BaseController {

	function __construct(Attribute $repo, AttributeGroup $attribute_groups, REST $rest) {
		$this->attribute_groups = $attribute_groups;
		$this->repo = $repo;
		$this->rest = $rest;
	}
	/**
	 * Display a listing of attributes
	 *
	 * @return Response
	 */
	public function index()
	{
		return $this->rest->response(200, $this->repo->with('group')->get());
	}

	/**
	 * Store a newly created attribute in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$attribute_group_id = Input::get('attribute_group_id');
		$this->attribute_groups->findOrFail($attribute_group_id);

		$attribute = new $this->repo;

		if ($attribute->save())
		{
			return $this->rest->response(201, $attribute);
		}

		return $this->response->errorBadRequest($attribute->errors());
	}

	/**
	 * Display the specified attribute.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$attribute = $this->repo->findOrFail($id);

		return $this->rest->response(200, $attribute);
	}

	/**
	 * Update the specified attribute in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$attribute = $this->repo->findOrFail($id);
		$attribute_group_id = Input::get('attribute_group_id');
		$this->attribute_groups->findOrFail($attribute_group_id);
		
		if ($attribute->save())
		{
			return $this->rest->response(202, $attribute);
		}

		return $this->response->errorBadRequest($attribute->errors());
	}

	/**
	 * Remove the specified attribute from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$attribute = $this->repo->findOrFail($id);

		if ($this->repo->destroy($id)) {
			return $this->rest->response(202, $attribute);
		}

		return $this->response->errorBadRequest();
	}

}
