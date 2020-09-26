<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/* @property Price[] $prices
 * @property int $pk_id
 * @property string $ctg_name
 * @property int $ctg_quantity
 * @property float $ctg_purch_price
 * @property float $ctg_sill_price
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */

class Category extends Model {

    use SoftDeletes;
    /* The primary key for the model.
     * 
     * @var string
     */

    protected $dates = ['deleted_at'];
    protected $primaryKey = 'pk_id';
    protected $timestamp=true;
    /**
     * @var array
     */
    protected $fillable = ['ctg_name', 'ctg_quantity', 'ctg_purch_price', 'ctg_sill_price', 'ctg_total','created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prices() {
        return $this->hasMany('App\Price', 'fk_ctg', 'pk_id');
    }

}
