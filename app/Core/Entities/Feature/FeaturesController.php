<?php namespace App\Core\Entities\Feature;

use App\Core\Entities\Feature\FeatureRepository;
use Dingo\Api\Routing\ControllerTrait;

class FeaturesController extends \Controller {

	use ControllerTrait;

	protected $repository;

	function __construct(FeatureRepository $repository) {
		$this->repository = $repository;
	}
	
	/**
	 * Display a listing of the resource.
	 * GET /features
	 *
	 * @return Response
	 */
	public function index()
	{
		try {
			$limit = \Input::get('limit', false);

			$response = $this->repository->with(['values']);
			if (!($limit === false) && is_numeric($limit)) {
				$response = $response->paginate($limit);
			} else {
				$response = $response->all();
			}
			
			return $this->response->array($response->toArray());
		} catch (\Exception $e) {
			throw new \Dingo\Api\Exception\ResourceException("Error Processing Request", $e->getMessage());
			
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		try {
			$this->repository->getModel()->validate();

			$repository = $this->repository->create(\Input::all());

			return $this->response->array($repository->toArray());
		} catch (\LaravelBook\Ardent\InvalidModelException $e) {
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getErrors());
		} catch (\Exception $e) {
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getMessage());
		}
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
		try {
			$repository = $this->repository->with(['values'])->find($id);
			return $this->response->array($repository->toArray());
		} catch (\Exception $e) {
			throw new \Dingo\Api\Exception\ResourceException("Error Processing Request", $e->getMessage());
		}
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
		try {
			$repository = $this->repository->find($id);

			$this->repository->getModel()->validate();

			$repository->update(\Input::all(), $id);
			return $this->response->array($repository->toArray());
		} catch (\LaravelBook\Ardent\InvalidModelException $e) {
			throw new \Dingo\Api\Exception\UpdateResourceFailedException("Error Processing Request", $e->getErrors());
		} catch (\Exception $e) {
			throw new \Dingo\Api\Exception\UpdateResourceFailedException("Error Processing Request", $e->getMessage());
		}
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
		try {
			$repository = $this->repository->find($id);
			if ($this->repository->delete($id)) {
				return $this->response->array($repository->toArray());
			}
		} catch (\Exception $e) {
			throw new \Dingo\Api\Exception\DeleteResourceFailedException("Error Processing Request", $e->getMessage());
		}
	}

}