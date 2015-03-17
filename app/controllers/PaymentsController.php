<?php

class PaymentsController extends \BaseController {

	function __construct(Payment $repo, REST $rest) {
		$this->repo = $repo;
		$this->rest = $rest;
	}

	/**
	 * Display a listing of the resource.
	 * GET /payments
	 *
	 * @return Response
	 */
	public function index()
	{
		$payments = $this->repo->get();

		return $this->rest->response(200, $payments);
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /payments
	 *
	 * @return Response
	 */
	public function store()
	{
		$payment = new $this->repo;

		if ($payment->save()) {
			return $this->rest->response(201, $payment);
		}

		return $this->response->errorBadRequest($payment->errors());
	}

	/**
	 * Display the specified resource.
	 * GET /payments/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$payment = $this->repo->findOrFail($id);

		return $this->rest->response(201, $payment);
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /payments/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$payment = $this->repo->findOrFail($id);
		
		if ($payment->save()) {
			return $this->rest->response(201, $payment);
		}

		return $this->response->errorBadRequest($payment->errors());
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /payments/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$payment = $this->repo->findOrFail($id);
		
		if ($this->repo->destroy($id)) {
			return $this->rest->response(202, $payment);
		}

		return $this->response->errorBadRequest();
	}

}