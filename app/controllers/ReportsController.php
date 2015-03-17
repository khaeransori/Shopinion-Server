<?php

class ReportsController extends \BaseController {

	function __construct(Order $repo, REST $rest) {
		$this->repo = $repo;
		$this->rest = $rest;
	}

	/**
	 * Display a listing of reports
	 *
	 * @return Response
	 */
	public function index()
	{
		$month    = Input::get('month', 0);
		$year     = Input::get('year', 0);
		$monthly  = Input::get('monthly', 0);
		$from     = Input::get('from', 0);
		$to       = Input::get('to', 0);
		$periodic = Input::get('periodic', 0);

		$orders = $this->repo
						->with(
							'customer',
							'cart',
							'carrier',
							'detail',
							'delivery_address',
							'history.state',
							'invoice_address',
							'state',
							'payment'
							)
						->where('delivered', 1);
		if ($monthly === 1) {
			$orders = $orders->where( DB::raw('MONTH(created_at)', '=', $month ))
							 ->where( DB::raw('YEAR(created_at)', '=', $year ));
		}

		if ($periodic === 1) {
			$orders = $orders->whereBetween('created_at', [$from, $to]);
		}

		return $this->rest->response(200, $orders->get());
	}

}
