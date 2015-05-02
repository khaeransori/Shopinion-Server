<?php namespace App\Core\Entities\Category;

use App\Core\Entities\Category\CategoryRepository;
use Dingo\Api\Routing\ControllerTrait;
use Illuminate\Validation\Factory;

class CategoriesController extends \Controller {

	use ControllerTrait;

	protected $repository;
	protected $validator;

	function __construct(CategoryRepository $repository, Factory $validator) {
		$this->repository = $repository;
		$this->validator = $validator;
	}

	/**
	 * Display a listing of the resource.
	 * GET /categories
	 *
	 * @return Response
	 */
	public function index()
	{
		$root = $this->repository->getModel()->root();

		$active 				= \Input::get('active', 0);
		$all					= \Input::get('all', 1);
		$currentNode			= \Input::get('currentNode', 0);
		$depth					= \Input::get('depth', 0);
		$limit 					= \Input::get('limit', false);
		$withCurrent			= \Input::get('withCurrent', 1);
		$withCurrentDescendants = \Input::get('withCurrentDescendants', 1);
		$withRoot 				= \Input::get('withRoot', 0);

		if ($all) {
			$root = ($withRoot) ? $root->descendantsAndSelf() : $root->descendants();
		}

		if ($currentNode) {
			$currentNode = $this->repository->find($currentNode);
			$currentNodeDescendants = $currentNode->getModel()->getDescendants();

			$root = (!$withCurrent) ? $root->withoutNode($currentNode) : $root;
			if (!$withCurrentDescendants) {
				foreach ($currentNodeDescendants as $currentNodeDescendant) {
					$root->withoutNode($currentNodeDescendant);
				}
			}
		}

		$root = ($depth) ? $root->limitDepth($depth) : $root;

		if (! ($active === 0)) {
			$root = $root->where('active', 1);
		}
		
		$response = $root->with('children');
		if (!($limit === false) && is_numeric($limit)) {
			$response = $response->paginate($limit);
		} else {
			$response = $response->get();
		}

		return $this->response->array($response->toArray());
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /categories
	 *
	 * @return Response
	 */
	public function store()
	{

		$validator = $this->validator->make(\Input::all(), $this->repository->getModel()->getRules());
		if (\Input::has('parent_id')) {
			$parent = $this->repository->getModel()->findOrFail(\Input::get('parent_id'));
		}

		if ($validator->passes()) {
			$repository = $this->repository->create(\Input::only(['name', 'description', 'parent_id']));

			if (\Input::has('parent_id')) {
				$repository->getModel()->makeChildOf($parent);
			}

			return $this->response->array($repository->toArray());
		}

		throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $validator->messages());
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
		$active = \Input::get('active', 0);

		$repository = $this->repository
							->getModel()
							->with([
								'parent',
								'children' => function ($query) use ($active)
								{
									(! ($active === 0)) ? $query->where('active', 1) : '';
								}
							])
							->findOrFail($id);

		return $this->response->array($repository->toArray());
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
		$repository = $this->repository->getModel()->findOrFail($id);

		$validator = $this->validator->make(\Input::all(), $this->repository->getModel()->getRules());
		if (\Input::has('parent_id')) {
			$parent = $this->repository->getModel()->findOrFail(\Input::get('parent_id'));
		}

		if ($validator->passes()) {
			$this->repository->update(\Input::only(['parent_id', 'name', 'description', 'active']), $id);

			if (\Input::has('parent_id')) {
				$repository->getModel()->makeChildOf($parent);
			}

			return $this->response->array($repository->toArray());
		}

		throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $validator->messages());
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
		$repository = $this->repository->getModel()->findOrFail($id);
		if ($this->repository->delete($id)) {
			return $this->response->array($repository->toArray());
		}

		throw new \Dingo\Api\Exception\DeleteResourceFailedException("Error Processing Request", 1);
	}

}