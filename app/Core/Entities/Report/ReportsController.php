<?php namespace App\Core\Entities\Report;

use App\Core\Entities\Order\OrderRepository;
use Dingo\Api\Routing\ControllerTrait;

class ReportsController extends \Controller {

	use ControllerTrait;

	protected $repository;

	function __construct(OrderRepository $repository) {
		$this->repository = $repository;
	}

	/**
	 * Display a listing of reports
	 *
	 * @return Response
	 */
	public function index()
	{
		$month    = \Input::get('month', 0);
		$year     = \Input::get('year', 0);
		$monthly  = \Input::get('monthly', false);
		$from     = \Input::get('from', 0);
		$to       = \Input::get('to', 0);
		$periodic = \Input::get('periodic', 0);
		$limit 	  = \Input::get('limit', false);

		$orders = $this->repository
						->getModel()
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
		if (!($monthly === false)) {
			$orders = $orders->where( \DB::raw('MONTH(created_at)'), $month)
							 ->where( \DB::raw('YEAR(created_at)'), $year);
		}

		if (!($periodic === 0)) {
			$orders = $orders->whereBetween('created_at', [$from, $to]);
		}

		if (!($limit === false) && is_numeric($limit)) {
			$response = $orders->paginate($limit);
		} else {
			$response = $orders->get();
		}

		return $this->response->array($response->toArray());
	}

}
