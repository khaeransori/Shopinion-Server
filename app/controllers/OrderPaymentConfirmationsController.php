<?php

class OrderPaymentConfirmationsController extends \BaseController {

	/**
	 * Display a listing of orderpaymentconfirmations
	 *
	 * @return Response
	 */
	public function index()
	{
		$orderpaymentconfirmations = Orderpaymentconfirmation::all();

		return View::make('orderpaymentconfirmations.index', compact('orderpaymentconfirmations'));
	}

	/**
	 * Show the form for creating a new orderpaymentconfirmation
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('orderpaymentconfirmations.create');
	}

	/**
	 * Store a newly created orderpaymentconfirmation in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Orderpaymentconfirmation::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Orderpaymentconfirmation::create($data);

		return Redirect::route('orderpaymentconfirmations.index');
	}

	/**
	 * Display the specified orderpaymentconfirmation.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$orderpaymentconfirmation = Orderpaymentconfirmation::findOrFail($id);

		return View::make('orderpaymentconfirmations.show', compact('orderpaymentconfirmation'));
	}

	/**
	 * Show the form for editing the specified orderpaymentconfirmation.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$orderpaymentconfirmation = Orderpaymentconfirmation::find($id);

		return View::make('orderpaymentconfirmations.edit', compact('orderpaymentconfirmation'));
	}

	/**
	 * Update the specified orderpaymentconfirmation in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$orderpaymentconfirmation = Orderpaymentconfirmation::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Orderpaymentconfirmation::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$orderpaymentconfirmation->update($data);

		return Redirect::route('orderpaymentconfirmations.index');
	}

	/**
	 * Remove the specified orderpaymentconfirmation from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Orderpaymentconfirmation::destroy($id);

		return Redirect::route('orderpaymentconfirmations.index');
	}

}
