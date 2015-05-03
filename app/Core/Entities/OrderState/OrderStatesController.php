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
			throw new \Dingo\Api\Exception\ResourceException("Error Processing Request", $e->getMessage());
		}
	}

}