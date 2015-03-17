<?php

class OrderDetailsController extends \BaseController {

	/**
	 * Display a listing of orderdetails
	 *
	 * @return Response
	 */
	public function index()
	{
		$orderdetails = Orderdetail::all();

		return View::make('orderdetails.index', compact('orderdetails'));
	}

	/**
	 * Show the form for creating a new orderdetail
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('orderdetails.create');
	}

	/**
	 * Store a newly created orderdetail in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Orderdetail::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Orderdetail::create($data);

		return Redirect::route('orderdetails.index');
	}

	/**
	 * Display the specified orderdetail.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$orderdetail = Orderdetail::findOrFail($id);

		return View::make('orderdetails.show', compact('orderdetail'));
	}

	/**
	 * Show the form for editing the specified orderdetail.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$orderdetail = Orderdetail::find($id);

		return View::make('orderdetails.edit', compact('orderdetail'));
	}

	/**
	 * Update the specified orderdetail in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$orderdetail = Orderdetail::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Orderdetail::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$orderdetail->update($data);

		return Redirect::route('orderdetails.index');
	}

	/**
	 * Remove the specified orderdetail from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Orderdetail::destroy($id);

		return Redirect::route('orderdetails.index');
	}

}
