<?php namespace App\Core\Entities\StockMovementReason;

use App\Core\Criteria\GetBySignCriteria;
use App\Core\Entities\StockMovementReason\StockMovementReasonRepository;
use Dingo\Api\Routing\ControllerTrait;

class StockMovementReasonsController extends \Controller {

	use ControllerTrait;

	protected $repository;

	function __construct(StockMovementReasonRepository $repository) {
		$this->repository = $repository;
	}

	/**
	 * Display a listing of the resource.
	 * GET /stockmovementreasons
	 *
	 * @return Response
	 */
	public function index()
	{
		try {
			$limit = \Input::get('limit', false);
			
			$this->repository->pushCriteria(new GetBySignCriteria());

			$response = $this->repository;
			if (!($limit === false) && is_numeric($limit)) {
				$response = $response->paginate($limit);
			} else {
				$response = $response->all();
			}

			return $this->response->array($response->toArray());
		} catch (Exception $e) {
			throw new Dingo\Api\Exception\ResourceException("Error Processing Request", $e->errors());
			
		}
	}
}