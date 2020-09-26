<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $comit_date
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property CommitteePatient[] $committeePatients
 */
class Committee extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['comit_name', 'comit_date', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function committeePatients()
    {
        return $this->hasMany('App\CommitteePatient');
    }
}
