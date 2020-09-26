<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $personal_id
 * @property boolean $is_avatar
 * @property string $upload_name
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property PatientPersonal $patientPersonal
 */
class Upload extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['personal_id', 'is_avatar', 'upload_name', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patientPersonal()
    {
        return $this->belongsTo('App\PatientPersonal', 'personal_id');
    }
}
