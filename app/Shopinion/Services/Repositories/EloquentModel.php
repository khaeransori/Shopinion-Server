<?php namespace Shopinion\Services\Repositories;

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use LaravelBook\Ardent\Ardent;
use Carbon\Carbon;

class EloquentModel extends Ardent
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
}