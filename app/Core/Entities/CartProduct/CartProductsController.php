<?php namespace App\Core\Entities\CartProduct;

use App\Core\Criteria\GetByCartIdCriteria;
use App\Core\Criteria\GetByProductIdCriteria;
use App\Core\Criteria\GetByProductAttributeIdCriteria;
use App\Core\Entities\Cart\CartRepository;
use App\Core\Entities\CartProduct\CartProductRepository;
use App\Core\Entities\Customer\CustomerRepository;
use App\Core\Entities\Product\ProductRepository;
use App\Core\Entities\ProductAttribute\ProductAttributeRepository;
use Dingo\Api\Routing\ControllerTrait;

class CartProductsController extends \Controller {

	use ControllerTrait;

	protected $cart;
	protected $customer;
	protected $product;
	protected $product_attribute;
	protected $repository;

	function __construct(
		CartProductRepository $repository, 
		CartRepository $cart, 
		CustomerRepository $customer,
		ProductRepository $product, 
		ProductAttributeRepository $product_attribute
	) {
		$this->cart = $cart;
		$this->customer = $customer;
		$this->product = $product;
		$this->product_attribute = $product_attribute;
		$this->repository = $repository;
	}

	/**
	 * Display a listing of cartproducts
	 *
	 * @return Response
	 */
	public function index()
	{
		try {
			$limit = \Input::get('limit', false);

			$this->repository->pushCriteria(new GetByCartIdCriteria());
			$response = $this->repository->with(['product.images',  'combination.attribute_combinations.group']);

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
	 * Store a newly created cartproduct in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		try {
			$this->repository->getModel()->validate();

			$cart_id 				= \Input::get('cart_id');
			$product_id 			= \Input::get('product_id');
			$product_attribute_id 	= \Input::get('product_attribute_id', 0);
			$qty 					= \Input::get('qty', 0);

			$errors = ['qty' => ['Available stock is not enough to add to cart']];

			$cart = $this->cart->find($cart_id);
			$product = $this->product->with(['productStock'])->find($product_id);

			if (!($product_attribute_id === 0)) {
				$product_attribute = $this->product_attribute->with(['stock'])->find($product_attribute_id);

				if ($product_attribute->stock->qty < $qty) {
					throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $errors);
				}
			} else {
				if ($product->product_stock->qty < $qty) {
					throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $errors);
				}
			}

			$this->repository->pushCriteria(new GetByProductIdCriteria());
			
			if (!($product_attribute_id === 0)) {
				$this->repository->pushCriteria(new GetByProductAttributeIdCriteria());
			}

			try {
				$repository = $this->repository->findByField('cart_id', $cart_id);
				$this->repository->update(\Input::all(), $repository->id);
			} catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
				$repository = $this->repository->scopeReset()->create(\Input::all());
			}

			$this->repository->with(['product.images',  'combination.attribute_combinations.group'])->find($repository->id);

			return $this->response->array($repository->toArray());
		} catch (\LaravelBook\Ardent\InvalidModelException $e) {
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getErrors());
		} catch (\Exception $e) {
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getMessage());
		}
	}

	/**
	 * Remove the specified cartproduct from storage.
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

	///////////////////////////////
	public function storeCustomer()
	{
		try {
	        if ($user = \JWTAuth::parseToken()->authenticate()) {

	        	$customer = $this->customer->findByField('user_id', $user->id);

	        	$cart = $this->cart
								->getModel()
								->where('customer_id', $customer->id)
								->where('is_customer', 1)
								->where('ordered', 0)
								->get()
								->first();

				$cart_id 				= $cart->id;
				$product_id 			= \Input::get('product_id');
				$product_attribute_id 	= \Input::get('product_attribute_id', 0);
				$qty 					= \Input::get('qty', 0);

				$data = array(
					'cart_id' => $cart_id,
					'product_id' => $product_id,
					'product_attribute_id' => $product_attribute_id,
					'qty' => $qty
				);

				$errors = ['qty' => ['Available stock is not enough to add to cart']];

				$product = $this->product->with(['productStock'])->find($product_id);

				if (!($product_attribute_id === 0)) {
					$product_attribute = $this->product_attribute->with(['stock'])->find($product_attribute_id);

					if ($product_attribute->stock->qty < $qty) {
						throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $errors);
					}
				} else {
					if ($product->product_stock->qty < $qty) {
						throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $errors);
					}
				}

				$this->repository->pushCriteria(new GetByProductIdCriteria());
				
				if (!($product_attribute_id === 0)) {
					$this->repository->pushCriteria(new GetByProductAttributeIdCriteria());
				}
				try {
					$repository = $this->repository->findByField('cart_id', $cart_id);
					$this->repository->update($data, $repository->id);
				} catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
					$repository = $this->repository->scopeReset()->create($data);
				}

				$this->repository->with(['product.images',  'combination.attribute_combinations.group'])->find($repository->id);

				$response = array(
					'id'  => $cart->id,
					'qty' => $cart->products()->count()
				);
				return $this->response->array($response);
	        }
	    } catch (\LaravelBook\Ardent\InvalidModelException $e) {
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getErrors());
	    } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
			throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    } catch (\Exception $e) {
	    	throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    }
	}

	public function destroyCustomer($id)
	{
		try {
	        if ($user = \JWTAuth::parseToken()->authenticate()) {

	        	$repository = $this->repository->find($id);
	        	$customer = $this->customer->findByField('user_id', $user->id);

	        	$cart = $this->cart
								->getModel()
								->where('customer_id', $customer->id)
								->where('is_customer', 1)
								->where('ordered', 0)
								->get()
								->first();
								
				if ($this->repository->delete($id)) {
					$response = array(
						'id'  => $cart->id,
						'qty' => $cart->products()->count()
					);
					return $this->response->array($response);
				}

				throw new \Dingo\Api\Exception\DeleteResourceFailedException("Error Processing Request", 1);
	        }
	    } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
			throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    } catch (\Exception $e) {
	    	throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    }
	}
}
