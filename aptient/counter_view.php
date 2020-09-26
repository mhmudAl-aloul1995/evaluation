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
class counter_view extends Model
{
        use SoftDeletes;

    /**
     * The primary key for the model.
     * 
     * @var string
     */
        protected $table='counter_view';

    protected $primaryKey = 'pk_id';


    public function transaction()
    {

        return $this->hasMany(Transaction::class);
    }

}
