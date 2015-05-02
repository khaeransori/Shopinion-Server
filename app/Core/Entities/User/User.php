<?php namespace App\Core\Entities\User;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Rhumsaa\Uuid\Uuid;
use Zizaco\Confide\ConfideUser;
use Zizaco\Confide\ConfideUserInterface;
use Zizaco\Entrust\HasRole;

/**
 * User
 *
 * @property string $id
 * @property string $email
 * @property string $password
 * @property string $confirmation_code
 * @property string $remember_token
 * @property boolean $confirmed
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereConfirmationCode($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereConfirmed($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereDeletedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\Config::get('entrust::role')[] $roles
 * @property-read \Customer $customer
 */
class User extends \Eloquent implements ConfideUserInterface {

	use ConfideUser;
	use HasRole;
	use SoftDeletingTrait;

	protected $dates = ['deleted_at'];
	protected $hidden = array('password', 'confirmation_code', 'remember_token', 'deleted_at');
	protected $softDelete = true; 
    
	public function customer()
	{
		return $this->hasOne('Customer');
	}

	/**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
	public $incrementing = false;

	public function getCreatedAtAttribute($value)
	{
	    $dt = new Carbon($value);
	    $datetime = $dt->toIso8601String();

	    return $datetime;
	}

	public function getUpdatedAtAttribute($value)
	{
		$dt = new Carbon($value);
	    $datetime = $dt->toIso8601String();

	    return $datetime;
	}

	public function getDeletedAtAttribute($value)
	{
		$dt = new Carbon($value);
	    $datetime = $dt->toIso8601String();

	    return $datetime;
	}
}
