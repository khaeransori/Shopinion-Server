<?php

class CategoriesController extends \BaseController {

	function __construct(Category $repo, REST $rest) {
		$this->repo = $repo;
		$this->rest = $rest;
	}
	/**
	 * Display a listing of the resource.
	 * GET /categories
	 *
	 * @return Response
	 */
	public function index()
	{
		$root = $this->repo->root();

		$all					= Input::get('all', 1);
		$currentNode			= Input::get('currentNode', 0);
		$depth					= Input::get('depth', 0);
		$withCurrent			= Input::get('withCurrent', 1);
		$withCurrentDescendants = Input::get('withCurrentDescendants', 1);
		$withRoot 				= Input::get('withRoot', 0);

		if ($all) {
			$root = ($withRoot) ? $root->descendantsAndSelf() : $root->descendants();
		}

		if ($currentNode) {
			$currentNode = $this->repo->findOrFail($currentNode);
			$currentNodeDescendants = $currentNode->getDescendants();

			$root = (!$withCurrent) ? $root->withoutNode($currentNode) : $root;
			if (!$withCurrentDescendants) {
				foreach ($currentNodeDescendants as $currentNodeDescendant) {
					$root->withoutNode($currentNodeDescendant);
				}
			}
		}

		$root = ($depth) ? $root->limitDepth($depth) : $root;

		return $this->rest->response(200, $root->with('children')->get());
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /categories
	 *
	 * @return Response
	 */
	public function store()
	{
		$rules = array(
			'name' 			=> 'required|min:3',
			'description'	=> 'required',
			'active'		=> 'required'
		);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->passes()) {
			$node = $this->repo->create(array(
					'name' 			=> Input::get('name'),
					'description'	=> Input::get('description'),
					'active'		=> Input::get('active')
				));

			if (Input::has('parent_id')) {
				$parent = $this->repo->findOrFail(Input::get('parent_id'));

				$node->makeChildOf($parent);
			}

			return $this->rest->response(201, $node);
		}
		
		return $this->response->errorBadRequest($validator->messages());
	}

	/**
	 * Display the specified resource.
	 * GET /categories/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$category = $this->repo->with('children', 'children.children')->findOrFail($id);

		return $this->rest->response(200, $category);
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /categories/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$category = $this->repo->findOrFail($id);

		$rules = array(
			'name' 			=> 'required|min:3',
			'description'	=> 'required',
			'active'		=> 'required'
		);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->passes()) {
			$category->update(array(
					'name' 			=> Input::get('name'),
					'description'	=> Input::get('description'),
					'active'		=> Input::get('active')
				));

			if (Input::has('parent_id')) {
				$parent = $this->repo->findOrFail(Input::get('parent_id'));

				$category->makeChildOf($parent);
			}

			return $this->rest->response(201, $category);
		}
		
		return $this->response->errorBadRequest($validator->messages());
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /categories/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$category = $this->repo->with('children')->findOrFail($id);

		if ($this->repo->destroy($id)) {
			return $this->rest->response(202, $category);
		}

		return $this->response->errorBadRequest();
	}

}