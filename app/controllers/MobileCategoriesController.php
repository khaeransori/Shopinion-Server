<?php

class MobileCategoriesController extends \BaseController {

	function __construct(Category $repo, REST $rest) {
		$this->repo = $repo;
		$this->rest = $rest;
	}

	/**
	 * Display a listing of the resource.
	 * GET /mobilecategories
	 *
	 * @return Response
	 */
	public function index()
	{
		$root = $this->repo->root();
		$root->load([
			'children' => function ($children)
			{
				$children->whereActive(1)
							->orderBy('name', 'ASC');
			}
			]);

		return $this->rest->response(200, $root);
	}

	/**
	 * Display the specified resource.
	 * GET /mobilecategories/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$category = $this->repo->findOrFail($id);
		$category->load([
			'children' => function ($children)
			{
				$children->whereActive(1)
							->orderBy('name', 'ASC');
			},
			'parent'
			]);

		return $this->rest->response(200, $category);
	}

}