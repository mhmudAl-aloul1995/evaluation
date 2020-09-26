<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property PatientMedical[] $patientMedicals
 */
class Weapon extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['weapon_name', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function patientMedicals()
    {
        return $this->hasMany('App\PatientMedical');
    }
}
