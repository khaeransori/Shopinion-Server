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
			throw new \Dingo\Api\Exception\ResourceException("Error Processing Request", $e->getMessage());
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
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getMessage());
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
		try {
			$repository = $this->repository->find($id);
			return $this->response->array($repository->toArray());
		} catch (\Exception $e) {
			throw new \Dingo\Api\Exception\ResourceException("Error Processing Request", $e->getMessage());
		}
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
			throw new \Dingo\Api\Exception\UpdateResourceFailedException("Error Processing Request", $user->errors());

		} catch (\Exception $e) {
			\DB::rollBack();
			throw new \Dingo\Api\Exception\UpdateResourceFailedException("Error Processing Request", $e->getMessage());
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
		try {
			if ($user = \JWTAuth::parseToken()->authenticate()) {
				if ($user->id === $id) {
					throw new \Dingo\Api\Exception\DeleteResourceFailedException("Cannot Delete Yourself");
				}
	        }

			$repository = $this->repository->find($id);
			if ($this->repository->delete($id)) {
				return $this->response->array($repository->toArray());
			}
		} catch (\Exception $e) {
			throw new \Dingo\Api\Exception\DeleteResourceFailedException("Error Processing Request", $e->getMessage());
		}
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

    public function login()
    {
    	// grab credentials from the request
	    $credentials = \Input::only('email', 'password');

	    try {
	        // attempt to verify the credentials and create a token for the user
	        if (! $token = \JWTAuth::attempt($credentials)) {
	        	throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Wrong Username or Password");
	        }
	    } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
	        // something went wrong whilst attempting to encode the token
	        throw new Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException("Could not create token");
	    }

	    $user = \JWTAuth::authenticate($token);

	    $response = array(
			'account' 			=> $user,
			'token'   			=> $token
	    );

	    return $this->response->array($response);
    }
}
