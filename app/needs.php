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
class needs extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['need_name', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function patientMedicals()
    {
        return $this->hasMany('App\patient_medical','need_id');
    }
}
