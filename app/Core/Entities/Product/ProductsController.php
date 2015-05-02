<?php namespace App\Core\Entities\Product;

use App\Core\Criteria\ActiveCriteria;
use App\Core\Criteria\GetByCategoryCriteria;
use App\Core\Criteria\GetByCategoriesCriteria;
use App\Core\Criteria\GetByManufacturerCriteria;
use App\Core\Criteria\SaleCriteria;
use App\Core\Entities\Customer\CustomerRepository;
use App\Core\Entities\Category\CategoryRepository;
use App\Core\Entities\Product\ProductRepository;
use App\Core\Entities\Stock\StockRepository;
use Dingo\Api\Routing\ControllerTrait;
use Illuminate\Support\Facades\DB;

class ProductsController extends \Controller {

	use ControllerTrait;

	protected $category;
	protected $customer;
	protected $repository;
	protected $stock;
	
	function __construct(CategoryRepository $category, CustomerRepository $customer, ProductRepository $repository, StockRepository $stock) {
		$this->category = $category;
		$this->customer = $customer;
		$this->repository = $repository;
		$this->stock = $stock;
	}
	/**
	 * Display a listing of the resource.
	 * GET /products
	 *
	 * @return Response
	 */
	public function index()
	{
		try {

			$active = \Input::get('active', 0);
			$category = \Input::get('category', 0);
			$categories = \Input::get('categories', 0);
			$limit = \Input::get('limit', false);
			$manufacturer = \Input::get('manufacturer', 0);
			$sale = \Input::get('sale', 0);

			if (! ($active === 0)) {
				$this->repository->pushCriteria(new ActiveCriteria());
			}

			if (! ($category === 0)) {
				$this->repository->pushCriteria(new GetByCategoryCriteria());
			}

			if (! ($categories === 0)) {
				$current = $this->category->getModel()->find($categories);
				$categoriesCollection = $current->getDescendantsAndSelf();
				$categoriesIdCollection = array();
				foreach ($categoriesCollection as $category) {
					$categoriesIdCollection[] = $category['id'];
				}

				// return $categoriesIdCollection;
				$this->repository->pushCriteria(new GetByCategoriesCriteria($categoriesIdCollection));
			}

			if (! ($manufacturer === 0)) {
				$this->repository->pushCriteria(new GetByManufacturerCriteria());
			}

			if (! ($sale === 0)) {
				$this->repository->pushCriteria(new SaleCriteria());
			}

			$response = $this->repository->with(
				[
					'productStock',
					'aggregateStock',
					'categories',
					'category',
					'combinations.attribute_combinations.group',
					'combinations.stock',
					'features',
					'images'
				]
			);
			
			if (!($limit === false) && is_numeric($limit)) {
				$response = $response->paginate($limit);
			} else {
				$response = $response->all();
			}

			return $this->response->array($response->toArray());
		} catch (\Exception $e) {
			return $e;
			throw new \Dingo\Api\Exception\ResourceException("Error Processing Request", $e->errors());
			
		}
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /products
	 *
	 * @return Response
	 */
	public function store()
	{
		DB::beginTransaction();

		try {
			$this->repository->getModel()->validate();
			
			$repository = $this->repository->create(\Input::all());

			#create zero stock
			$stock = array(
				'product_id'           => $repository->id,
				'product_attribute_id' => 0,
				'qty'                  => 0
			);

			$this->stock->create($stock);
			DB::commit();
			if (\Input::has('categories') && is_array(\Input::get('categories'))) {
				$repository->categories()->sync(\Input::get('categories'));
			}

			DB::rollBack();
			return $this->response->array($repository->toArray());
		} catch (\LaravelBook\Ardent\InvalidModelException $e) {
			DB::rollBack();
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getErrors());
		}
	}

	/**
	 * Display the specified resource.
	 * GET /products/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$repository = $this->repository->with(
			[
				'productStock',
				'aggregateStock',
				'categories',
				'category',
				'combinations.attribute_combinations.group',
				'combinations.stock',
				'features.feature',
				'images',
				'manufacturer'
			])->find($id);

		try {
			if ($user = \JWTAuth::parseToken()->authenticate()) {
				$is_exist = $this->customer
									->getModel()
									->where('user_id', $user->id)
									->whereHas('wishlist', function ($query) use ($id) {
										$query->where('product_id', $id);
									})->count();

				$repository['is_wishlist'] = ($is_exist > 0) ? 1 : 0;
			}
		} catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {}
		

		return $this->response->array($repository->toArray());
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /products/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		try {
			$toggle = \Input::get('toggle', 0);
			$repository = $this->repository->getModel()->findOrFail($id);

			if ($repository->updateUniques()) {
				if ($toggle === 0) {
					if (\Input::has('categories') && is_array(\Input::get('categories'))) {
						$repository->categories()->sync(\Input::get('categories'));
					}

					if (\Input::has('features') && is_array(\Input::get('features'))) {
						$repository->features()->sync(\Input::get('features'));
					}
				}

				return $this->response->array($repository->toArray());
			}
		} catch (\LaravelBook\Ardent\InvalidModelException $e) {
			throw new \Dingo\Api\Exception\UpdateResourceFailedException("Error Processing Request", $e->getErrors());
		}
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /products/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$repository = $this->repository->with(['combinations', 'productStock'])->find($id);

		if ($repository->product_stock->qty > 0) {
			$errors = ['message' => ['You cannot delete this product because there\'s physical stock left.']];
			throw new \Dingo\Api\Exception\DeleteResourceFailedException("Error Processing Request", 1);
		}

		if (count($repository->combinations) > 0) {
			$errors = ['message' => ['You cannot delete this product because this product still have combinations.']];
			throw new \Dingo\Api\Exception\DeleteResourceFailedException("Error Processing Request", 1);
		}

		if ($this->repository->delete($id)) {
			return $this->response->array($repository->toArray());
		}

		throw new \Dingo\Api\Exception\DeleteResourceFailedException("Error Processing Request", 1);
	}

}