<?php namespace App\Core\Entities\PaymentConfirmation;

use App\Core\Entities\BankAccount\BankAccountRepository;
use App\Core\Entities\Order\OrderRepository;
use App\Core\Entities\OrderHistory\OrderHistoryRepository;
use App\Core\Entities\OrderState\OrderStateRepository;
use App\Core\Entities\PaymentConfirmation\PaymentConfirmationRepository;
use Dingo\Api\Routing\ControllerTrait;
use Illuminate\Validation\Factory;

class PaymentConfirmationsController extends \Controller {

	use ControllerTrait;

	protected $bank_account;
	protected $history;
	protected $order;
	protected $repository;
	protected $state;
	protected $validator;

	function __construct(
		PaymentConfirmationRepository $repository, 
		OrderRepository $order, 
		OrderHistoryRepository $history,
		OrderStateRepository $state,
		BankAccountRepository $bank_account,
		Factory $validator
	) {
		$this->bank_account = $bank_account;
		$this->order        = $order;
		$this->repository   = $repository;
		$this->state        = $state;
		$this->history      = $history;
		$this->validator 	= $validator;
	}

	/**
	 * Store a newly created paymentconfirmation in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		\DB::beginTransaction();

		try {
	        if ($user = \JWTAuth::parseToken()->authenticate()) {
	        	$order_id        = \Input::get('order_id');
				$bank_account_id = \Input::get('bank_account_id');
				$ammount         = \Input::get('ammount');
				$date_paid       = \Carbon\Carbon::parse(\Input::get('date_paid'))->format('Y/m/d');

				$order        = $this->order->find($order_id);
				$bank_account = $this->bank_account->find($bank_account_id);

				$data_to_add = array(
					'order_id' => $order_id,
					'bank_account_id' => $bank_account_id,
					'ammount' => $ammount,
					'date_paid' => $date_paid
				);

				$validator = $this->validator->make($data_to_add, $this->repository->getModel()->getRules());

				if ($validator->passes()) {
					$repository = $this->repository->create($data_to_add);

					$state = $this->state->findByField('order', 3);
					$current_state = $state->id;

					$this->order->update(['current_state' => $current_state], $order_id);

					$history_to_add = array(
						'order_id' => $order_id,
						'order_state_id' => $current_state
					);

					$this->history->create($history_to_add);

					\DB::commit();
					return $this->response->array($repository->toArray());
				}

				\DB::rollBack();
				throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $validator->messages());
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
