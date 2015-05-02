<?php namespace Shopinion\Services\Repositories;

use Baum\Node;
use Carbon\Carbon;
use Rhumsaa\Uuid\Uuid;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * Category
 *
 * @property string $id
 * @property string $parent_id
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property string $name
 * @property string $description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property-read \get_class($this $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\get_class($this[] $children
 * @method static \Illuminate\Database\Query\Builder|\Category whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Category whereParentId($value)
 * @method static \Illuminate\Database\Query\Builder|\Category whereLft($value)
 * @method static \Illuminate\Database\Query\Builder|\Category whereRgt($value)
 * @method static \Illuminate\Database\Query\Builder|\Category whereDepth($value)
 * @method static \Illuminate\Database\Query\Builder|\Category whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Category whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Category whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Category whereDeletedAt($value)
 * @method static \Baum\Node withoutNode($node)
 * @method static \Baum\Node withoutSelf()
 * @method static \Baum\Node withoutRoot()
 * @method static \Baum\Node limitDepth($limit)
 * @property-read \Illuminate\Database\Eloquent\Collection|\Product[] $products
 * @property boolean $active
 * @method static \Illuminate\Database\Query\Builder|\Category whereActive($value)
 */
class EloquentNodeModel extends Node {

  use SoftDeletingTrait;

  protected $dates = ['deleted_at'];
  protected $hidden = ['deleted_at'];
  /**
   * Indicates if the IDs are auto-incrementing.
   *
   * @var bool
   */
  public $incrementing = false;

  /**
   * Table name.
   *
   * @var string
   */
  protected $table = 'categories';

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
  
  //////////////////////////////////////////////////////////////////////////////

  //
  // Below come the default values for Baum's own Nested Set implementation
  // column names.
  //
  // You may uncomment and modify the following fields at your own will, provided
  // they match *exactly* those provided in the migration.
  //
  // If you don't plan on modifying any of these you can safely remove them.
  //

  // /**
  //  * Column name which stores reference to parent's node.
  //  *
  //  * @var string
  //  */
  // protected $parentColumn = 'parent_id';

  // /**
  //  * Column name for the left index.
  //  *
  //  * @var string
  //  */
  // protected $leftColumn = 'lft';

  // /**
  //  * Column name for the right index.
  //  *
  //  * @var string
  //  */
  // protected $rightColumn = 'rgt';

  // /**
  //  * Column name for the depth field.
  //  *
  //  * @var string
  //  */
  // protected $depthColumn = 'depth';

  // /**
  //  * Column to perform the default sorting
  //  *
  //  * @var string
  //  */
  protected $orderColumn = 'name';

  // /**
  // * With Baum, all NestedSet-related fields are guarded from mass-assignment
  // * by default.
  // *
  // * @var array
  // */
  // protected $guarded = array('id', 'parent_id', 'lft', 'rgt', 'depth');

  //
  // This is to support "scoping" which may allow to have multiple nested
  // set trees in the same database table.
  //
  // You should provide here the column names which should restrict Nested
  // Set queries. f.ex: company_id, etc.
  //

  // /**
  //  * Columns which restrict what we consider our Nested Set list
  //  *
  //  * @var array
  //  */
  // protected $scoped = array();

  //////////////////////////////////////////////////////////////////////////////

  //
  // Baum makes available two model events to application developers:
  //
  // 1. `moving`: fired *before* the a node movement operation is performed.
  //
  // 2. `moved`: fired *after* a node movement operation has been performed.
  //
  // In the same way as Eloquent's model events, returning false from the
  // `moving` event handler will halt the operation.
  //
  // Below is a sample `boot` method just for convenience, as an example of how
  // one should hook into those events. This is the *recommended* way to hook
  // into model events, as stated in the documentation. Please refer to the
  // Laravel documentation for details.
  //
  // If you don't plan on using model events in your program you can safely
  // remove all the commented code below.
  //

  /**
   * The "booting" method of the model.
   *
   * @return void
   */
  protected static function boot() {
    // Do not forget this!
    parent::boot();

    /**
     * Attach to the 'creating' Model Event to provide a UUID
     * for the `id` field (provided by $model->getKeyName())
     */
    static::creating(function ($model)
    {
      $model->{$model->getKeyName()} = (string)$model->generateNewId();
    });

    static::deleted(function ($model)
    {
      $model->children()->delete();
    });
    // static::moving(function($node) {
    //   // YOUR CODE HERE
    // });

    // static::moved(function($node) {
    //   // YOUR CODE HERE
    // });
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
