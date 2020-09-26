<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $city_id
 * @property int $area_id
 * @property int $created_by
 * @property string $name
 * @property string $identity
 * @property string $phone
 * @property string $dob
 * @property boolean $sex
 * @property string $masjisd
 * @property string $year_graduation
 * @property string $child_no
 * @property int $qualification
 * @property string $grade
 * @property int $economic_situt
 * @property int $social_situt
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Area $area
 * @property City $city
 * @property User $user
 * @property Agent[] $agents
 * @property DisService[] $disServices
 * @property PatientMedical[] $patientMedicals
 */
class patient_personal extends Model {

    /**
     * The table associated with the model.
     * 
     * @var string
     */
     use SoftDeletes;
    protected $table = 'patient_personal';

    /**
     * @var array
     */
    protected $fillable = ['city_id', 'area_id', 'created_by', 'name', 'identity', 'phone', 'dob', 'sex', 'masjisd', 'year_graduation', 'child_no', 'qualification', 'grade', 'economic_situt', 'social_situt', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function area() {
        return $this->belongsTo('App\Area');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city() {
        return $this->belongsTo('App\City', 'city_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function committeePatients() {
        return $this->hasMany('App\CommitteePatient', 'personal_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function agents() {
        return $this->hasMany('App\Agent', 'personal_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function disServices() {
        return $this->hasMany('App\dis_service', 'personal_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function patientMedicals() {
        return $this->hasMany('App\patient_medical', 'personal_id');
    }

    public function patient_travel() {
        return $this->hasMany('App\patient_travel', 'personal_id');
    }

}
