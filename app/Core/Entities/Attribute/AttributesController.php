<?php namespace App\Core\Entities\Attribute;

use App\Core\Entities\Attribute\AttributeRepository;
use App\Core\Entities\AttributeGroup\AttributeGroupRepository;
use Dingo\Api\Routing\ControllerTrait;

class AttributesController extends \Controller {

	use ControllerTrait;

	protected $repository;
	protected $attribute_groups;

	function __construct(AttributeGroupRepository $attribute_groups, AttributeRepository $repository) {
		$this->attribute_groups = $attribute_groups;
		$this->repository = $repository;
	}

	/**
	 * Display a listing of attributes
	 *
	 * @return Response
	 */
	public function index()
	{
		try {
			$limit = \Input::get('limit', false);

			$response = $this->repository->with(['group']);
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
	 * Store a newly created attribute in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		try {
			$this->repository->getModel()->validate();

			$this->attribute_groups->find(\Input::get('attribute_group_id'));

			$repository = $this->repository->create(\Input::all());

			return $this->response->array($repository->toArray());
		} catch (\LaravelBook\Ardent\InvalidModelException $e) {
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getErrors());
		} catch (\Exception $e) {
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getMessage());
		}
	}

	/**
	 * Display the specified attribute.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		try {
			$repository = $this->repository->find($id);

			return $this->response->array($repository->toArray());
		} catch (\Exception $e) {
			throw new \Dingo\Api\Exception\ResourceException("Error Processing Request", $e->getMessage());
			
		}
	}

	/**
	 * Update the specified attribute in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		try {
			$repository = $this->repository->find($id);
			$this->attribute_groups->find(\Input::get('attribute_group_id'));

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
	 * Remove the specified attribute from storage.
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
			throw new \Dingo\Api\Eception\DeleteResourceFailedException("Error Processing Request", $e->getMessage());
		}
	}

}
