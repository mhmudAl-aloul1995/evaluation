<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $created_by
 * @property int $personal_id
 * @property string $name
 * @property string $identity
 * @property string $phone
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property User $user
 * @property PatientPersonal $patientPersonal
 */
class Agent extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['created_by', 'personal_id', 'agent_name', 'agent_identity', 'agent_phone', 'created_at', 'updated_at', 'deleted_at'];

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
