<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $pk_id
 * @property int $fk_customer
 * @property string $messages
 */
class Messages_sent extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'messages_sent';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'pk_id';

    /**
     * @var array
     */
    protected $fillable = ['fk_customer', 'messages','counter_date'];

}
