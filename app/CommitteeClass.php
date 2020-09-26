<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $committee_class_name
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property CommitteePatient[] $committeePatients
 */
class CommitteeClass extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'committee_class';

    /**
     * @var array
     */
    protected $fillable = ['committee_class_name', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function committeePatients()
    {
        return $this->hasMany('App\CommitteePatient');
    }
}
