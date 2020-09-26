<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $pk_id
 * @property float $t_import
 * @property float $t_export
 * @property float $t_balance
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class Transaction extends Model
{
        use SoftDeletes;

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'pk_id';
   protected $table = 'transactions';
    /**
     * @var array
     */
    protected $fillable = ['fk_customer','t_credit', 't_debit', 't_balance','statment', 'created_at', 'updated_at', 'deleted_at'];

    public function counter_view()
    {

        return $this->belongsTo(counter_view::class,'ctr_fk_debit','pk_id');
    }
}
