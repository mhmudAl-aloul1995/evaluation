<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $need_id
 * @property int $committee_class_id
 * @property int $created_by
 * @property int $personal_id
 * @property int $committee_id
 * @property string $treatment_duration
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property CommitteeClass $committeeClass
 * @property Committee $committee
 * @property User $user
 * @property Need $need
 * @property PatientPersonal $patientPersonal
 */
class CommitteePatient extends Model {

    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'committee_patient';

    /**
     * @var array
     */
    protected $fillable = ['need_id', 'committee_class_id', 'created_by', 'personal_id', 'committee_id', 'treatment_duration', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function committeeClass() {
        return $this->belongsTo('App\CommitteeClass');
    }

    public function oldChild() {
        return $this->hasOne('App\Committee')->whereRaw('commit_name = (select max(`comit_name`) from committees)');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function committee() {
        return $this->belongsTo('App\Committee');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function need() {
        return $this->belongsTo('App\Need');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patientPersonal() {
        return $this->belongsTo('App\patient_personal', 'personal_id');
    }

}
