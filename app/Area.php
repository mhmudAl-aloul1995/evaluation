<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 */
class Area extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['area_name', 'deleted_at', 'created_at', 'updated_at'];

}
