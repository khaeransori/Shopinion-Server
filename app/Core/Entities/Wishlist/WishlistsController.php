<?php namespace App\Core\Entities\Wishlists;

use App\Core\Criteria\ActiveCriteria;
use App\Core\Entities\Customer\CustomerRepository;
use App\Core\Entities\Product\ProductRepository;
use Dingo\Api\Routing\ControllerTrait;

class WishlistsController extends \Controller {

	use ControllerTrait;

	protected $repository;
	protected $product;

	function __construct(CustomerRepository $repository, ProductRepository $product) {
		$this->repository = $repository;
		$this->product  = $product;
	}

	/**
	 * Display a listing of the resource.
	 * GET /wishlist
	 *
	 * @return Response
	 */
	public function index()
	{
	    $limit = \Input::get('limit', false);

		try {
	        if ($user = \JWTAuth::parseToken()->authenticate()) {
	        	$repository 	= $this->repository->findByField('user_id', $user->id);

				$this->product->pushCriteria(new ActiveCriteria());

	        	$response = $this->product
							->getModel()
							->with(
								'productStock',
								'aggregateStock',
								'categories',
								'category',
								'combinations.attribute_combinations.group',
								'combinations.stock',
								'features',
								'images'
							)
							->whereHas('wishlist', function ($query) use ($repository) {
								$query->where('customer_id', $repository->id);
							});
			
				if (!($limit === false) && is_numeric($limit)) {
					$response = $response->paginate($limit);
				} else {
					$response = $response->get();
				}

				return $this->response->array($response->toArray());
	        }
	    } catch (\Dingo\Api\Exception\StoreResourceFailedException $e) {
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getErrors());
	    } catch (\LaravelBook\Ardent\InvalidModelException $e) {
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getErrors());
	    } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
			throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    } catch (\Exception $e) {
			throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    }
	}

	/**
	 * Store a newly created wishlist in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
	    $product_id  = \Input::get('product_id');
		
		try {
	        if ($user = \JWTAuth::parseToken()->authenticate()) {
				$product 		= $this->product->find($product_id);
	        	$repository 	= $this->repository->findByField('user_id', $user->id);

	        	$is_exist = $this->repository
									->getModel()
									->where('user_id', $user->id)
									->whereHas('wishlist', function ($query) use ($product_id) {
										$query->where('product_id', $product_id);
									})->count();

				if ($is_exist === 0) {
					$repository->wishlist()->attach($product_id);
				}

				$wishlist = $repository->getModel()->wishlist()->count();

				$response = array('wishlist' => $wishlist);
				return $this->response->array($response);
	        }
	    } catch (\Dingo\Api\Exception\StoreResourceFailedException $e) {
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getErrors());
	    } catch (\LaravelBook\Ardent\InvalidModelException $e) {
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getErrors());
	    } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
			throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    } catch (\Exception $e) {
			throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    }
	}

	/**
	 * Remove the specified wishlist from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		try {
	        if ($user = \JWTAuth::parseToken()->authenticate()) {
	        	$product 		= $this->product->find($id);
	        	$repository 	= $this->repository->findByField('user_id', $user->id);

	        	$is_exist = $this->repository
									->getModel()
									->where('user_id', $user->id)
									->whereHas('wishlist', function ($query) use ($id) {
										$query->where('product_id', $id);
									})->count();

				if ($is_exist > 0) {
					$repository->wishlist()->detach($id);
				}

				$wishlist = $repository->getModel()->wishlist()->count();

				$response = array('wishlist' => $wishlist);
				return $this->response->array($response);
	        }
	    } catch (\Dingo\Api\Exception\StoreResourceFailedException $e) {
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getErrors());
	    } catch (\LaravelBook\Ardent\InvalidModelException $e) {
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getErrors());
	    } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
			throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    } catch (\Exception $e) {
			throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    }
	}

}
