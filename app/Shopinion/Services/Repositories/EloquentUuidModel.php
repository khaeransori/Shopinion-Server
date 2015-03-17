<?php namespace Shopinion\Services\Repositories;

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use LaravelBook\Ardent\Ardent;
use Rhumsaa\Uuid\Uuid;

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
		return strtotime($value) * 1000;
	}

	public function getUpdatedAtAttribute($value)
	{
		return strtotime($value) * 1000;
	}

	public function getDeletedAtAttribute($value)
	{
		return ($value == null) ? null : strtotime($value) * 1000;
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