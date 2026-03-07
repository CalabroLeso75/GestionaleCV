<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceAssignment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'data_assegnazione' => 'datetime',
        'data_restituzione' => 'datetime',
    ];

    public function assignable()
    {
        return $this->morphTo();
    }

    public function assignee()
    {
        return $this->morphTo();
    }
    
    public function logs()
    {
        return $this->hasMany(ResourceAssignmentLog::class);
    }
}
