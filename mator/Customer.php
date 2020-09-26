<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $pk_id
 * @property int $cs_fk_area
 * @property string $cs_name
 * @property string $cs_mobile
 * @property string $cs_subscripe_date
 * @property boolean $cs_is_counter
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Area $area
 */
class Customer extends Model
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
    protected $fillable = ['cus_index', 'cs_fk_area', 'p_enabled', 'cs_name', 'cs_mobile', 'cs_subscripe_date', 'cs_is_counter', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function area()
    {
        return $this->belongsTo('App\Area', 'cs_fk_area', 'pk_id');
    }

    public function counter()
    {
        return $this->hasMany('App\Counter', 'ctr_customer_id', 'pk_id');
    }

}
