<?php namespace App\Core\Entities\ProductAttribute;

use App\Core\Criteria\GetByProductIdCriteria;
use App\Core\Entities\Product\ProductRepository;
use App\Core\Entities\ProductAttribute\ProductAttributeRepository;
use App\Core\Entities\Stock\StockRepository;
use Dingo\Api\Routing\ControllerTrait;
use Illuminate\Support\Facades\DB;

class ProductAttributesController extends \Controller {

	use ControllerTrait;

	protected $product;
	protected $repository;
	protected $stock;

	function __construct(ProductAttributeRepository $repository, ProductRepository $product, StockRepository $stock) {
		$this->product = $product;
		$this->repository = $repository;
		$this->stock = $stock;
	}
	/**
	 * Display a listing of the resource.
	 * GET /productattributes
	 *
	 * @return Response
	 */
	public function index()
	{
		try {
			$product_id = \Input::get('product_id');
			$limit = \Input::get('limit', false);

			$this->product->find($product_id);

			$this->repository->pushCriteria(new GetByProductIdCriteria());

			$response = $this->repository->with(['attribute_combinations', 'attribute_combinations.group', 'stock']);
			if (!($limit === false) && is_numeric($limit)) {
				$response = $response->paginate($limit);
			} else {
				$response = $response->all();
			}

			return $this->response->array($response->toArray());
		} catch (\Exception $e) {
			throw new Dingo\Api\Exception\ResourceException("Error Processing Request", $e->getMessage());
			
		}
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /productattributes
	 *
	 * @return Response
	 */
	public function store()
	{
		DB::beginTransaction();

		try {
			$product_id = \Input::get('product_id');
			$attributes = \Input::get('attributes', 0);

			$this->product->find($product_id);

			if ((! ($attributes === 0)) && (! is_array($attributes))) {
				$errors = ['attributes' => ['The attributes field is required.']];
				DB::rollBack();
				throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $errors);
			}

			$this->repository->getModel()->validate();

			$repository = $this->repository->create(\Input::all());

			#create zero stock
			$stock = array(
				'product_id'           => $product_id,
				'product_attribute_id' => $repository->id,
				'qty'                  => 0
			);

			$this->stock->create($stock);

			if (\Input::get('default_on')) {
				$this->repository
						->getModel()
						->where('product_id', \Input::get('product_id'))
						->update(array('default_on' => 0));

				$this->repository
						->getModel()
						->where('id', $repository->id)
						->update(array('default_on' => 1));
			}

			$repository->attribute_combinations()->attach($attributes); #ini yang masih jadi PR, buat relasinya
			$repository =$repository->with(['attribute_combinations', 'attribute_combinations.group', 'stock'])->find($repository->id);

			DB::commit();
			return $this->response->array($repository->toArray());
		} catch (\LaravelBook\Ardent\InvalidModelException $e) {
			DB::rollBack();
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getErrors());
		} catch (\Exception $e) {
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getMessage());
		}
	}

	/**
	 * Display the specified resource.
	 * GET /productattributes/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		try {
			$repository = $this->repository->with(['attribute_combinations', 'attribute_combinations.group', 'stock'])->find($id);
			return $this->response->array($repository->toArray());
		} catch (\Exception $e) {
			throw new \Dingo\Api\Exception\ResourceException("Error Processing Request", $e->getMessage());
		}
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /productattributes/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		DB::beginTransaction();
		try {
			$attributes = \Input::get('attributes', 0);
			$default_on = \Input::get('default_on', 0);
			$product_id = \Input::get('product_id');

			$repository = $this->repository->find($id);
			$this->product->find($product_id);

			$old_default_on = $repository->default_on;

			if ((! ($attributes === 0)) && (! is_array($attributes))) {
				$errors = ['attributes' => ['The attributes field is required.']];
				DB::rollBack();
				throw new \Dingo\Api\Exception\UpdateResourceFailedException("Error Processing Request", $errors);
			}

			$this->repository->getModel()->validate();

			$this->repository->update(\Input::all(), $id);

			$repository->attribute_combinations()->sync($attributes);

			if (!($default_on === 0)) {
				$this->repository
						->getModel()
						->where('product_id', $repository->product_id)
						->update(array('default_on' => 0));

				$this->repository
						->getModel()
						->where('id', $repository->id)
						->update(array('default_on' => 1));
			} else {
				if ($old_default_on === 1) {
					$this->repository
							->getModel()
							->where('product_id', $repository->product_id)
							->take(1)
							->update(array('default_on' => 1));
				}
			}

			$repository =$repository->with(['attribute_combinations', 'attribute_combinations.group', 'stock'])->find($id);

			DB::commit();
			return $this->response->array($repository->toArray());
		} catch (\LaravelBook\Ardent\InvalidModelException $e) {
			DB::rollBack();
			throw new \Dingo\Api\Exception\UpdateResourceFailedException("Error Processing Request", $e->getErrors());
		} catch (\Exception $e) {
			throw new \Dingo\Api\Exception\UpdateResourceFailedException("Error Processing Request", $e->getMessage());
		}
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /productattributes/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		try {
			$repository = $this->repository->find($id);
			if ($this->repository->delete($id)) {
				$new_default = $this->repository->findByField('product_id', $repository->product_id);
				$this->repository->update(['default_on' => 1], $new_default->id);

				return $this->response->array($repository->toArray());
			}
		} catch (\Exception $e) {
			throw new \Dingo\Api\Exception\DeleteResourceFailedException("Error Processing Request", $e->getMessage());
		}
	}
}
