<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $committee_decision_name
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 */
class committee_decision extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'committee_decision';

    /**
     * @var array
     */
    protected $fillable = ['committee_decision_name', 'deleted_at', 'created_at', 'updated_at'];

}
