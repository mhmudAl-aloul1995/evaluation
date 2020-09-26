<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $created_by
 * @property string $name
 * @property string $description
 * @property string $date
 * @property string $value
 * @property int $place
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 * @property DisService[] $disServices
 */
class Service extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['created_by', 'service_name', 'description', 'date', 'value', 'place', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function disServices()
    {
        return $this->hasMany('App\DisService');
    }
}
