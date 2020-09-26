<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $app_type_id
 * @property int $created_by
 * @property int $personal_id
 * @property string $diagnosis
 * @property string $travel_place
 * @property string $travel_back_date
 * @property string $travel_date_comit
 * @property string $travel_date
 * @property int $committee_decision_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property AppType $appType
 * @property User $user
 * @property PatientPersonal $patientPersonal
 */
class patient_travel extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'patient_travel';

    /**
     * @var array
     */
    protected $fillable = ['app_type_id', 'created_by', 'personal_id', 'diagnosis', 'travel_place', 'travel_back_date', 'travel_date_comit', 'travel_date', 'committee_decision_id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function appType()
    {
        return $this->belongsTo('App\AppType');
    }

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
    public function patientPersonal()
    {
        return $this->belongsTo('App\PatientPersonal', 'personal_id');
    }
}
