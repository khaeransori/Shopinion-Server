<?php

use Dingo\Api\Routing\ControllerTrait;

class PaymentConfirmationsController extends Controller {

	use ControllerTrait;

	function __construct(
		PaymentConfirmation $repo, 
		Order $order, 
		OrderHistory $history,
		OrderState $state,
		REST $rest,
		BankAccount $bank_account) {

		$this->protect('store');

		$this->bank_account = $bank_account;
		$this->order        = $order;
		$this->repo         = $repo;
		$this->state        = $state;
		$this->rest         = $rest;
		$this->history      = $history;
	}
	/**
	 * Display a listing of paymentconfirmations
	 *
	 * @return Response
	 */
	public function index()
	{
		$paymentconfirmations = Paymentconfirmation::all();

		return View::make('paymentconfirmations.index', compact('paymentconfirmations'));
	}

	/**
	 * Store a newly created paymentconfirmation in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$order_id        = Input::get('order_id');
		$bank_account_id = Input::get('bank_account_id');
		$ammount         = Input::get('ammount');
		$str             = strtotime(Input::get('date_paid'));
		$date_paid       = date("Y/m/d", $str);

		$order        = $this->order->findOrFail($order_id);
		$bank_account = $this->bank_account->findOrFail($bank_account_id);

		DB::beginTransaction();
		try {
			$confirmation = new $this->repo;
			
			$confirmation->order_id 		= $order_id;
			$confirmation->bank_account_id 	= $bank_account_id;
			$confirmation->ammount 			= $ammount;
			$confirmation->date_paid 		= $date_paid;

			if ($confirmation->save()) {

				$state         = $this->state->where('order', 3)->first();
				$current_state = $state->id;

				$order->current_state = $current_state;
				if ($order->save()) {
					$history = array(
						'order_id' => $order->id,
						'order_state_id' => $current_state
						);

					$this->history->create($history);
				}

				DB::commit();
				return $this->rest->response(201, $confirmation);
			}
		} catch (Exception $e) {
			DB::rollBack();
				
			return $this->response->errorBadRequest($e->getMessage());
		}
		
		return $this->response->errorBadRequest($confirmation->errors());

	}

	/**
	 * Display the specified paymentconfirmation.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$paymentconfirmation = Paymentconfirmation::findOrFail($id);

		return View::make('paymentconfirmations.show', compact('paymentconfirmation'));
	}

	/**
	 * Show the form for editing the specified paymentconfirmation.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$paymentconfirmation = Paymentconfirmation::find($id);

		return View::make('paymentconfirmations.edit', compact('paymentconfirmation'));
	}

	/**
	 * Update the specified paymentconfirmation in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$paymentconfirmation = Paymentconfirmation::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Paymentconfirmation::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$paymentconfirmation->update($data);

		return Redirect::route('paymentconfirmations.index');
	}

	/**
	 * Remove the specified paymentconfirmation from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Paymentconfirmation::destroy($id);

		return Redirect::route('paymentconfirmations.index');
	}

}
