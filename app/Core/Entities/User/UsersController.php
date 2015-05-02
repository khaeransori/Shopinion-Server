<?php namespace App\Core\Entities\User;

use App\Core\Entities\User\UserRepository;
use App\Core\Criteria\IsAdministratorCriteria;
use Dingo\Api\Routing\ControllerTrait;
use Rhumsaa\Uuid\Uuid;

class UsersController extends \Controller {

	use ControllerTrait;

	protected $repository;
	
	function __construct(UserRepository $repository) {
		$this->repository = $repository;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		try {

			// TODO
			// $active = \Input::get('active', 0);

			// if (! ($active === 0)) {
			// }
			$limit = \Input::get('limit', false);
			
			$this->repository->pushCriteria(new IsAdministratorCriteria());

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
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		\DB::beginTransaction();

		try {
			$user = $this->repository->getModel();
			$user->id 					 = (string) Uuid::uuid4();
			$user->email                 = \Input::get('email');
			$user->password              = \Input::get('password');
			$user->password_confirmation = \Input::get('password');
			$user->confirmation_code     = md5(uniqid(mt_rand(), true));
			$user->confirmed             = 1;
			
			if ($user->save()) {
				\DB::commit();

				if (\Config::get('confide::signup_email')) {
	                \Mail::queueOn(
	                    \Config::get('confide::email_queue'),
	                    \Config::get('confide::email_account_confirmation'),
	                    compact('user'),
	                    function ($message) use ($user) {
	                        $message
	                            ->to($user->email, $user->username)
	                            ->subject(\Lang::get('confide::confide.email.account_confirmation.subject'));
	                    }
	                );
	            }
				
				return $this->response->array($user->toArray());
			}

			\DB::rollBack();
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $user->errors());
		} catch (\Exception $e) {
			\DB::rollBack();
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getErrors());
		}
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$repository = $this->repository->find($id);
		return $this->response->array($repository->toArray());
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$user 			= $this->repository->getModel()->findOrFail($id);

		\DB::beginTransaction();

		try {
			if (\Input::has('email')) {
				$user->email = \Input::get('email');
			}

			if (\Input::has('password')) {
				$user->password = \Input::get('password');
				$user->password_confirmation = \Input::get('password_confirmation');
			}

			if ($user->save()) {
				\DB::commit();
				return $this->response->array($user->toArray());
			}

			\DB::rollBack();
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $user->errors());

		} catch (\Exception $e) {
			\DB::rollBack();
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->errors());
		}
	}


	/**
	 * Remove the specified resource from storage.
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

	/**
     * Attempt to confirm account with code
     *
     * @param  string $code
     *
     * @return  Illuminate\Http\Response
     */
    public function confirm($code)
    {
        if (\Confide::confirm($code)) {
            $notice_msg = \Lang::get('confide::confide.alerts.confirmation');
            return $notice_msg;
        } else {
            $error_msg = \Lang::get('confide::confide.alerts.wrong_confirmation');
            return $error_msg;
        }
    }

    /**
     * Shows the change password form with the given token
     *
     * @param  string $token
     *
     * @return  Illuminate\Http\Response
     */
    public function resetPassword($token)
    {
        return \View::make(\Config::get('confide::reset_password_form'))
                ->with('token', $token);
    }

    /**
     * Attempt change password of the user
     *
     * @return  Illuminate\Http\Response
     */
    public function doResetPassword()
    {
        $input = array(
            'token'                 => \Input::get('token'),
            'password'              => \Input::get('password'),
            'password_confirmation' => \Input::get('password_confirmation'),
        );

        // By passing an array with the token, password and confirmation
        $result = false;
        $user   = \Confide::userByResetPasswordToken($input['token']);

        if ($user) {
            $user->password              = $input['password'];
            $user->password_confirmation = $input['password_confirmation'];
            $result = $this->save($user);
        }

        // If result is positive, destroy token
        if ($result) {
            \Confide::destroyForgotPasswordToken($input['token']);
        }

        if ($result) {
            $notice_msg = \Lang::get('confide::confide.alerts.password_reset');
            return $notice_msg;
        } else {
            $error_msg = \Lang::get('confide::confide.alerts.wrong_password_reset');
            return $error_msg;
        }
    }
}
