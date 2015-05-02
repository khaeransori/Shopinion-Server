<?php namespace App\Core\Entities\CustomerAddress;

use App\Core\Entities\Customer\CustomerRepository;
use App\Core\Entities\CustomerAddress\CustomerAddressRepository;
use App\Core\Criteria\GetByCustomerIdCriteria;
use Dingo\Api\Routing\ControllerTrait;

class CustomerAddressesController extends \BaseController {

	use ControllerTrait;

	protected $customer;
	protected $repository;

	function __construct(CustomerAddressRepository $repository, CustomerRepository $customer) {
		$this->customer = $customer;
		$this->repository = $repository;
	}

	/**
	 * Display a listing of customeraddresses
	 *
	 * @return Response
	 */
	public function index()
	{
		try {
			$customer_id = \Input::get('customer_id');
			$limit = \Input::get('limit', false);
			
			$this->customer->find($customer_id);
			$this->repository->pushCriteria(new GetByCustomerIdCriteria());

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
	 * Store a newly created customeraddress in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		try {
			$customer_id = \Input::get('customer_id');
			$this->customer->find($customer_id);

			$this->repository->getModel()->validate();

			$repository = $this->repository->create(\Input::all());

			return $this->response->array($repository->toArray());
		} catch (\LaravelBook\Ardent\InvalidModelException $e) {
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getErrors());
		}
	}

	/**
	 * Display the specified customeraddress.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$repository = $this->repository->with(['customer'])->find($id);
		return $this->response->array($repository->toArray());
	}

	/**
	 * Update the specified customeraddress in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		try {
			$repository = $this->repository->find($id);
			$this->customer->find($repository->customer_id);

			$this->repository->getModel()->validate();

			$repository->update(\Input::all(), $id);
			return $this->response->array($repository->toArray());
		} catch (\LaravelBook\Ardent\InvalidModelException $e) {
			throw new \Dingo\Api\Exception\UpdateResourceFailedException("Error Processing Request", $e->getErrors());
		}
	}

	/**
	 * Remove the specified customeraddress from storage.
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

	/////////////////////////////
	public function getCustomer()
	{
		$limit = \Input::get('limit', false);

		try {
	        if ($user = \JWTAuth::parseToken()->authenticate()) {

	        	$customer 	= $this->customer->findByField('user_id', $user->id);

				$this->repository->pushCriteria(new GetByCustomerIdCriteria($customer->id));
				$response = $this->repository;
				if (!($limit === false) && is_numeric($limit)) {
					$response = $response->paginate($limit);
				} else {
					$response = $response->all();
				}
				return $this->response->array($response->toArray());
	        }
	    } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
			throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    } catch (\Exception $e) {
	    	throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    }
	}

	public function showCustomer($id)
	{
		try {
	        if ($user = \JWTAuth::parseToken()->authenticate()) {

	        	$customer 	= $this->customer->findByField('user_id', $user->id);
	        	
	        	$this->repository->pushCriteria(new GetByCustomerIdCriteria($customer->id));
				
				$repository = $this->repository->with(['customer'])->find($id);
				
				return $this->response->array($repository->toArray());
	        }
	    } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
			throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    } catch (\Exception $e) {
	    	throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    }
	}

	public function storeCustomer()
	{
		try {
	        if ($user = \JWTAuth::parseToken()->authenticate()) {
	        	$customer 	= $this->customer->findByField('user_id', $user->id);

	        	$address = array(
	        		'customer_id' 	=> $customer->id,
	        		'alias' 		=> \Input::get('alias'),
	        		'first_name' 	=> \Input::get('first_name'),
	        		'last_name' 	=> \Input::get('last_name'),
	        		'address' 		=> \Input::get('address'),
	        		'phone'			=> \Input::get('phone')
	        	);

				$repository = $this->repository->create($address);

				return $this->response->array($repository->toArray());
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

	public function updateCustomer($id)
	{
		try {
	        if ($user = \JWTAuth::parseToken()->authenticate()) {
	        	$customer 	= $this->customer->findByField('user_id', $user->id);

	        	$repository = $this->repository->find($id);
	        	
	        	$address = array(
	        		'alias' 		=> \Input::get('alias'),
	        		'first_name' 	=> \Input::get('first_name'),
	        		'last_name' 	=> \Input::get('last_name'),
	        		'address' 		=> \Input::get('address'),
	        		'phone'			=> \Input::get('phone')
	        	);

	        	$repository->update($address, $id);

				return $this->response->array($repository->toArray());
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
