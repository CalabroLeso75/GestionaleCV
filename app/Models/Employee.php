<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'birth_date' => 'date',
        'is_aib_qualified' => 'boolean',
        'is_emergency_available' => 'boolean',
        'operational_roles' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
