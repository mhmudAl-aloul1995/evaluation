<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $service_id
 * @property int $personal_id
 * @property int $created_by
 * @property string $date
 * @property string $value
 * @property int $place
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property User $user
 * @property PatientPersonal $patientPersonal
 * @property Service $service
 */
class Dis_service extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'dis_service';

    /**
     * @var array
     */
    protected $fillable = ['service_id', 'personal_id', 'created_by', 'service_date', 'value', 'place', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function PatientPersonal()
    {
        return $this->belongsTo('App\patient_personal', 'personal_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo('App\Service');
    }
}
