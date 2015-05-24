<?php namespace App\Core\Entities\Cart;

use App\Core\Criteria\GetByCustomerIdCriteria;
use App\Core\Criteria\IsCustomerCriteria;
use App\Core\Criteria\OrderedCriteria;
use App\Core\Entities\Carrier\CarrierRepository;
use App\Core\Entities\Cart\CartRepository;
use App\Core\Entities\Customer\CustomerRepository;
use App\Core\Entities\CustomerAddress\CustomerAddressRepository;
use Dingo\Api\Routing\ControllerTrait;

class CartsController extends \Controller {

	use ControllerTrait;

	protected $address;
	protected $carrier;
	protected $customer;
	protected $repository;

	function __construct(
		CartRepository $repository,
		CustomerRepository $customer,
		CustomerAddressRepository $address,
		CarrierRepository $carrier
	) {
		$this->repository 	= $repository;
		$this->customer = $customer;
		$this->address 	= $address;
		$this->carrier  = $carrier;
	}
	/**
	 * Display a listing of the resource.
	 * GET /carts
	 *
	 * @return Response
	 */
	public function index()
	{
		try {
			$customer_id = \Input::get('customer_id', false);
			$ordered = \Input::get('ordered', false);
			$limit = \Input::get('limit', false);

			if (!($customer_id === false)) {
				$this->customer->find($customer_id);
				$this->repository->pushCriteria(new GetByCustomerIdCriteria());
			}

			if (!($ordered === false)) {
				$this->repository->pushCriteria(new OrderedCriteria());
			}

			$response = $this->repository->with([
				'carrier',
				'customer',
				'delivery_address',
				'invoice_address',
				'order'
			]);
			
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
	 * Store a newly created resource in storage.
	 * POST /carts
	 *
	 * @return Response
	 */
	public function store()
	{
		try {
			$carrier_id 		 = \Input::get('carrier_id', 0);
			$customer_id 		 = \Input::get('customer_id'); 
			$delivery_address_id = \Input::get('delivery_address_id', 0);
			$invoice_address_id	 = \Input::get('invoice_address_id', 0);

			$customer = $this->customer->find($customer_id);

			if (! ($carrier_id === 0)) {
				$carrier = $this->carrier->find($carrier_id);

				if ((int) $carrier->on_store === (int) 0) {
					if (!($delivery_address_id === 0)) {
						$this->address->find($delivery_address_id);
					} else {
						$errors = ['delivery_address_id' => ['The delivery address field is required.']];
						throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $errors);
					}

					if (!($invoice_address_id === 0)) {
						$this->address->find($invoice_address_id);
					} else {
						$errors = ['invoice_address_id' => ['The invoice address field is required.']];
						throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $errors);
					}
				}
			}

			$this->repository->getModel()->validate();

			$repository = $this->repository->create(\Input::all());

			return $this->response->array($repository->toArray());
		} catch (\LaravelBook\Ardent\InvalidModelException $e) {
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getErrors());
		} catch (\Exception $e) {
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getMessage());
		}
	}

	/**
	 * Display the specified resource.
	 * GET /carts/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		try {
			$repository = $this->repository->with([
				'carrier',
				'customer.user',
				'delivery_address',
				'invoice_address',
				'products.product.images',
				'products.combination.attribute_combinations.group',
				'order'
			])->find($id);
			return $this->response->array($repository->toArray());
		} catch (\Exception $e) {
			throw new \Dingo\Api\Exception\ResourceException("Error Processing Request", $e->getMessage());
		}
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /carts/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		try {
			$this->repository->find($id);

			$carrier_id 		 = \Input::get('carrier_id', 0);
			$customer_id 		 = \Input::get('customer_id'); 
			$delivery_address_id = \Input::get('delivery_address_id', 0);
			$invoice_address_id	 = \Input::get('invoice_address_id', 0);

			$customer = $this->customer->find($customer_id);

			if (! ($carrier_id === 0)) {
				$carrier = $this->carrier->find($carrier_id);

				if ($carrier->on_store === 0) {
					if (!($delivery_address_id === 0)) {
						$this->address->find($delivery_address_id);
					} else {
						$errors = ['delivery_address_id' => ['The delivery address field is required.']];
						throw new \Dingo\Api\Exception\UpdateResourceFailedException("Error Processing Request", $errors);
					}

					if (!($invoice_address_id === 0)) {
						$this->address->find($invoice_address_id);
					} else {
						$errors = ['invoice_address_id' => ['The invoice address field is required.']];
						throw new \Dingo\Api\Exception\UpdateResourceFailedException("Error Processing Request", $errors);
					}
				}
			}

			$this->repository->getModel()->validate();

			$this->repository->update(\Input::all(), $id);
			$repository = $this->repository->find($id);

			return $this->response->array($repository->toArray());
		} catch (\LaravelBook\Ardent\InvalidModelException $e) {
			throw new \Dingo\Api\Exception\UpdateResourceFailedException("Error Processing Request", $e->getErrors());
		} catch (\Exception $e) {
			throw new \Dingo\Api\Exception\UpdateResourceFailedException("Error Processing Request", $e->getMessage());
		}
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /carts/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		try {
			$repository = $this->repository->find($id);
			if ($repository->ordered === 0) {
				if ($this->repository->delete($id)) {
					return $this->response->array($repository->toArray());
				}
			}
		} catch (\Exception $e) {
			throw new \Dingo\Api\Exception\DeleteResourceFailedException("Error Processing Request", $e->getMessage());
		}

	}

	/////////////////////////////
	public function getCustomer()
	{
		try {
	        if ($user = \JWTAuth::parseToken()->authenticate()) {

	        	$customer 	= $this->customer->findByField('user_id', $user->id);

				$this->repository->pushCriteria(new OrderedCriteria(0));
				$this->repository->pushCriteria(new IsCustomerCriteria(1));

				$response = $this->repository->with(['products.product.images', 'products.combination.attribute_combinations.group'])->findByField('customer_id', $customer->id);

				return $this->response->array($response->toArray());
	        }
	    } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
			throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    } catch (\Exception $e) {
	    	throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    }
	}
}