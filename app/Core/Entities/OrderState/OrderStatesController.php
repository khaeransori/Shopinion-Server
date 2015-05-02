<?php namespace App\Core\Entities\OrderState;

use App\Core\Entities\OrderState\OrderStateRepository;
use Dingo\Api\Routing\ControllerTrait;

class OrderStatesController extends \Controller {

	use ControllerTrait;

	protected $repository;

	function __construct(OrderStateRepository $repository) {
		$this->repository = $repository;
	}

	/**
	 * Display a listing of the resource.
	 * GET /orderstates
	 *
	 * @return Response
	 */
	public function index()
	{
		try {
			$limit = \Input::get('limit', false);

			$response = $this->repository;
			if (!($limit === false) && is_numeric($limit)) {
				$response = $response->paginate($limit);
			} else {
				$response = $response->all();
			}

			return $this->response->array($response->toArray());
		} catch (\Exception $e) {
			throw new \Dingo\Api\Exception\ResourceException("Error Processing Request", $e->errors());
			
		}
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /orderstates
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
		}
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
		$repository = $this->repository->find($id);
		return $this->response->array($repository->toArray());
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
		try {
			$repository = $this->repository->find($id);

			$this->repository->getModel()->validate();

			$repository->update(\Input::all(), $id);
			return $this->response->array($repository->toArray());
		} catch (\LaravelBook\Ardent\InvalidModelException $e) {
			throw new \Dingo\Api\Exception\UpdateResourceFailedException("Error Processing Request", $e->getErrors());
		}
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
		$repository = $this->repository->find($id);
		if ($this->repository->delete($id)) {
			return $this->response->array($repository->toArray());
		}

		throw new \Dingo\Api\Exception\DeleteResourceFailedException("Error Processing Request", 1);
	}

}