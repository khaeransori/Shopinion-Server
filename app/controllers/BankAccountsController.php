<?php

class BankAccountsController extends \BaseController {

	function __construct(BankAccount $repo, REST $rest) {
		$this->repo = $repo;
		$this->rest = $rest;
	}
	/**
	 * Display a listing of bankaccounts
	 *
	 * @return Response
	 */
	public function index()
	{
		$bankaccounts = $this->repo->all();

		return $this->rest->response(200, $bankaccounts);
	}

	/**
	 * Store a newly created bankaccount in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$bank = new $this->repo;

		if ($bank->save())
		{
			return $this->rest->response(201, $bank);
		}

		return $this->response->errorBadRequest($bank->errors());
	}

	/**
	 * Display the specified bankaccount.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$bank = $this->repo->findOrFail($id);

		return $this->rest->response(200, $bank);
	}

	/**
	 * Update the specified bankaccount in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$bank = $this->repo->findOrFail($id);
		
		if ($bank->save())
		{
			return $this->rest->response(202, $bank);
		}

		return $this->response->errorBadRequest($bank->errors());
	}

	/**
	 * Remove the specified bankaccount from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$bank = $this->repo->findOrFail($id);

		if ($this->repo->destroy($id)) {
			return $this->rest->response(202, $bank);
		}

		return $this->response->errorBadRequest();
	}

}
