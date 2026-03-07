<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyPhone extends Model
{
    protected $fillable = [
        'numero',
        'alias',
        'imei',
        'operatore',
        'piano_telefonico',
        'stato'
    ];

    public function teams()
    {
        return $this->belongsToMany(AibTeam::class, 'aib_team_phones', 'phone_id', 'team_id');
    }

    public function resourceAssignments()
    {
        return $this->morphMany(ResourceAssignment::class, 'assignable');
    }

    public function activeAssignment()
    {
        return $this->morphOne(ResourceAssignment::class, 'assignable')->whereNull('data_restituzione');
    }
}
