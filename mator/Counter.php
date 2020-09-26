<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $pk_id
 * @property int $ctr_customer_id
 * @property float $ctr_price
 * @property int $ctr_previous
 * @property int $ctr_minimum
 * @property string $ctr_date
 * @property string $ctr_current
 * @property int $ctr_ampair
 * @property int $ctr_amount
 * @property Customer $customer
 */
class Counter extends Model
{   
    use SoftDeletes;

    /**
     * The primary key for the model.
     * 
     * @var string
     */
     protected $table = 'counters';
    protected $primaryKey = 'pk_id';

    /**
     * @var array
     */
    protected $fillable = ['ctr_customer_id', 'ctr_price', 'ctr_previous', 'ctr_minimum', 'ctr_date', 'ctr_current', 'ctr_ampair', 'ctr_fk_debit'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo('App\Customer', 'ctr_customer_id', 'pk_id');
    }
}
