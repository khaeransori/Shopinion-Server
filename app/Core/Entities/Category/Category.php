<?php namespace App\Core\Entities\Category;

use Shopinion\Services\Repositories\EloquentNodeModel;

class Category extends EloquentNodeModel {

  protected $table = 'categories';

  protected $rules = array(
      'name'      => 'required|min:3',
      'description' => 'required|min:3',
      'parent_id' => 'max:36'
    );

  public function getRules()
  {
    return $this->rules;
  }

  public function products()
  {
      return $this->belongsToMany('\App\Core\Entities\Product\Product');
  }

  public function activeChildren()
  {
    return $this->hasMany('\App\Core\Entities\Category\Category', 'parent_id')->where('active', 1);
  }
}
