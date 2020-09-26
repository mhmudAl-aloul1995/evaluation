<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $diagnosis_name
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property PatientTravel[] $patientTravels
 */
class diagnosis extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'diagnosis';

    /**
     * @var array
     */
    protected $fillable = ['diagnosis_name', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function patientTravels()
    {
        return $this->hasMany('App\PatientTravel', 'diagnosis_id');
    }
}
