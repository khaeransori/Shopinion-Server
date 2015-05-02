<?php namespace App\Core\Entities\StockMovement;

use App\Core\Criteria\GetByProductIdCriteria;
use App\Core\Entities\Product\ProductRepository;
use App\Core\Entities\ProductAttribute\ProductAttributeRepository;
use App\Core\Entities\Stock\StockRepository;
use App\Core\Entities\StockMovement\StockMovementRepository;
use App\Core\Entities\StockMovementReason\StockMovementReasonRepository;
use Dingo\Api\Routing\ControllerTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Factory;

class StockMovementsController extends \Controller {

	use ControllerTrait;

	protected $repository;
	protected $movement;
	protected $reason;
	protected $product;
	protected $product_attribute;
	protected $validator;

	function __construct(
		Factory $validator,
		ProductRepository $product,
		ProductAttributeRepository $product_attribute,
		StockRepository $repository,
		StockMovementRepository $movement,
		StockMovementReasonRepository $reason
	) {
		$this->movement 			= $movement;
		$this->product 				= $product;
		$this->product_attribute 	= $product_attribute;
		$this->reason 				= $reason;
		$this->repository 			= $repository;
		$this->validator 			= $validator;
	}

	/**
	 * Display a listing of stocks
	 *
	 * @return Response
	 */
	public function index()
	{
		try {
			$limit = \Input::get('limit', false);

			$response = $this->movement->getModel()->withTrashed()
								->with(
									[
										'stock.product',
										'stock.combination.attribute_combinations.group',
										'reason'
									]
								);
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
	 * Store a newly created stock in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		DB::beginTransaction();

		try {
			$stock_movement_reason_id 	= \Input::get('stock_movement_reason_id');
			$product_id 				= \Input::get('product_id');
			$product_attribute_id 		= \Input::get('product_attribute_id', 0);
			$qty 						= \Input::get('qty');

			$errors = ['qty' => ['Available stock is not enough to decreased']];

			$product = $this->product->find($product_id);
			$reason = $this->reason->find($stock_movement_reason_id);

			if (\Input::has('product_attribute_id') && !($product_attribute_id === 0)) {
				$this->product_attribute->find($product_attribute_id);

				$repository = $this->repository->findByField('product_attribute_id', $product_attribute_id);
			} else {
				$this->repository->pushCriteria(new GetByProductIdCriteria());
				$repository = $this->repository->findByField('product_attribute_id', 0);
			}

			$qty_to_add = ($reason->sign * $qty);

			if (($reason->sign < 0) && ($repository->qty < $qty)) {
				throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $errors);
			}

			$validator = $this->validator->make(\Input::all(), $this->repository->getModel()->getRules());
			
			if ($validator->passes()) {
				$qty_now = $repository->qty + $qty_to_add;
				$this->repository->update(['qty'=>$qty_now], $repository->id);

				$data_movement = array(
					'stock_id' 					=> $repository->id,
					'qty'	   					=> $qty,
					'stock_movement_reason_id'	=> $stock_movement_reason_id
				);

				$movement_validator = $this->validator->make($data_movement, $this->movement->getModel()->getRules());

				if ($movement_validator->passes()) {
					$this->movement->create($data_movement);

					DB::commit();
					return $this->response->array($repository->toArray());
				}

				DB::rollBack();
				throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $movement_validator->messages());
			}

			DB::rollBack();
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $validator->messages());

		} catch (\LaravelBook\Ardent\InvalidModelException $e) {
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getErrors());
		}
	}
}
