<?php namespace App\Core\Entities\ProductImage;

use App\Core\Entities\ProductImage\ProductImageRepository;
use App\Core\Entities\Product\ProductRepository;
use App\Core\Criteria\GetByProductIdCriteria;
use Dingo\Api\Routing\ControllerTrait;
use GrahamCampbell\Flysystem\FlysystemManager;
use Illuminate\Validation\Factory;

class ProductImagesController extends \Controller {

	use ControllerTrait;

	protected $flysystem;
	protected $product;
	protected $repository;
	protected $validator;

	function __construct(Factory $validator, FlysystemManager $flysystem, ProductImageRepository $repository, ProductRepository $product) {
		$this->flysystem = $flysystem;
		$this->product = $product;
		$this->repository = $repository;
		$this->validator = $validator;
	}

	/**
	 * Display a listing of the resource.
	 * GET /productimages
	 *
	 * @return Response
	 */
	public function index()
	{
		try {
			$product_id	 = \Input::get('product_id', 0);
			$limit = \Input::get('limit', false);

			if (! ($product_id === 0)) {
				$this->product->find($product_id);
				$this->repository->pushCriteria(new GetByProductIdCriteria());
			}

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

	/**
	 * Store a newly created resource in storage.
	 * POST /productimages
	 *
	 * @return Response
	 */
	public function store()
	{
		\DB::beginTransaction();
		try {
			$product_id = \Input::get('product_id');
			$file = \Input::file('file');

			$this->product->find($product_id);

			$rules = array(
				'product_id' => 'required|max:36',
				'file'		 => 'required|image'
			);

			$validator = $this->validator->make(\Input::all(), $rules);

			if ($validator->passes()) {
				$repository = $this->repository->create(\Input::all());


				$filename  = $repository->id . '.png';
			    $cloudPath = 'images/product/' . $product_id . '/' . $repository->id . '/';

			    // $this->flysystem = \Flysystem::connection();

			    $default = \Image::make($file)->resize(null, 800, function ($constraint)
			    {
			    	$constraint->aspectRatio();
			    })->encode('png');

			    $large = \Image::make($file)->resize(null, 458, function ($constraint)
			    {
			    	$constraint->aspectRatio();
			    })->encode('png');

			    $medium = \Image::make($file)->resize(null, 125, function ($constraint)
			    {
			    	$constraint->aspectRatio();
			    })->encode('png');

			    $small = \Image::make($file)->resize(null, 98, function ($constraint)
			    {
			    	$constraint->aspectRatio();
			    })->encode('png');

			    $cart = \Image::make($file)->resize(null, 80, function ($constraint)
			    {
			    	$constraint->aspectRatio();
			    })->encode('png');
			    // $default = \Image::make($file)->fit(800)->encode('png');
			    // $large 	 = \Image::make($file)->fit(458)->encode('png');
			    // $medium  = \Image::make($file)->fit(125)->encode('png');
			    // $small 	 = \Image::make($file)->fit(98)->encode('png');
			    // $cart 	 = \Image::make($file)->fit(80)->encode('png');

			    $this->flysystem->put($cloudPath . $filename, (string) $default);
			    $this->flysystem->put($cloudPath . "large_" . $filename, (string) $default);
			    $this->flysystem->put($cloudPath . "medium_" . $filename, (string) $default);
			    $this->flysystem->put($cloudPath . "small_" . $filename, (string) $default);
			    $this->flysystem->put($cloudPath . "cart_" . $filename, (string) $default);

			    \DB::commit();
				return $this->response->array($repository->toArray());
			}
			
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $validator->messages());
		} catch (\Exception $e) {
			return $e;
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getErrors());
		}
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /productimages/{id}
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