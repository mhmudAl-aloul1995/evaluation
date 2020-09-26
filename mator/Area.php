<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $pk_id
 * @property string $area_name
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Customer[] $customers
 */
class Area extends Model
{  
    use SoftDeletes;

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'pk_id';

    /**
     * @var array
     */
    protected $fillable = ['area_name', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customers()
    {
        return $this->hasMany('App\Customer', 'cs_fk_area', 'pk_id');
    }
}
