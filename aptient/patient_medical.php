<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $weapon_id
 * @property int $status_desc_id
 * @property int $place_id
 * @property int $created_by
 * @property int $personal_id
 * @property string $date
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property User $user
 * @property PatientPersonal $patientPersonal
 * @property Place $place
 * @property StatusDesc $statusDesc
 * @property Weapon $weapon
 */
class patient_medical extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'patient_medical';

    /**
     * @var array
     */
    protected $fillable = ['weapon_id', 'status_desc_id', 'place_id', 'created_by', 'personal_id', 'date', 'created_at', 'updated_at', 'deleted_at'];

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
        return $this->belongsTo('App\patient_personal', 'personal_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function place()
    {
        return $this->belongsTo('App\Place');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function statusDesc()
    {
        return $this->belongsTo('App\status_desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function weapon()
    {
        return $this->belongsTo('App\Weapon');
    }
}
