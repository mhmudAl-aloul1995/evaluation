<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $pk_id
 * @property int $fk_customer
 * @property float $r_amount_paid
 * @property string $r_statement
 * @property boolean $r_recp_no
 * @property boolean $r_recp_book_no
 * @property string $r_date
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class Receipt extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'pk_id';

    /**
     * @var array
     */
    protected $fillable = ['fk_customer', 'r_amount_paid', 'r_statement', 'r_recp_no', 'r_recp_book_no', 'r_date', 'created_at', 'updated_at', 'deleted_at'];


    public function customer()
    {
        return $this->belongsTo('App\Customer', 'fk_customer', 'pk_id');
    }
    public function transaction()
    {
        return $this->belongsTo('App\Transaction', 'fk_transaction', 'pk_id');
    }
}
