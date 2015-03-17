<?php

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
class User extends Eloquent implements ConfideUserInterface {

	use ConfideUser;
	use HasRole;
	use SoftDeletingTrait;

    protected $dates = ['deleted_at'];
	protected $hidden = array('password', 'confirmation_code', 'remember_token', 'deleted_at');

	/**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
	public $incrementing = false;

	/**
     * The "booting" method of the model.
     *
     * @return void
     */
	public static function boot()
	{
		parent::boot();

		/**
         * Attach to the 'creating' Model Event to provide a UUID
         * for the `id` field (provided by $model->getKeyName())
         */
		static::creating(function ($model)
		{
			$model->{$model->getKeyName()} = (string)$model->generateNewId();
		});
	}

	/**
     * Get a new version 4 (random) UUID.
     *
     * @return \Rhumsaa\Uuid\Uuid
     */
	public function generateNewId()
	{
		return Uuid::uuid4();
	}

	public function customer()
	{
		return $this->hasOne('Customer');
	}

	public function getCreatedAtAttribute($value)
	{
		return strtotime($value);
	}

	public function getUpdatedAtAttribute($value)
	{
		return strtotime($value);
	}

	public function getDeletedAtAttribute($value)
	{
		return ($value == null) ? null : strtotime($value);
	}
}
