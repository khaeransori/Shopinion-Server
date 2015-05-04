<?php namespace App\Core\Services\Repositories;

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use LaravelBook\Ardent\Ardent;
use Rhumsaa\Uuid\Uuid;
use Carbon\Carbon;

class EloquentUuidModel extends Ardent
{
	use SoftDeletingTrait;

	protected $dates = ['deleted_at'];
	protected $hidden = ['deleted_at'];
	protected $softDelete = true; 

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
}