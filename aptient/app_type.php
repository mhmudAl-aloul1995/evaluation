<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $app_type_name
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property PatientTravel[] $patientTravels
 */
class app_type extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'app_type';

    /**
     * @var array
     */
    protected $fillable = ['app_type_name', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function patientTravels()
    {
        return $this->hasMany('App\PatientTravel');
    }
}
