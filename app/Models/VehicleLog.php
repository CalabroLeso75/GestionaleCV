<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'km_iniziali',
        'km_finali',
        'assegnato_il',
        'riconsegnato_il',
        'note',
    ];

    protected $casts = [
        'assegnato_il' => 'datetime',
        'riconsegnato_il' => 'datetime',
        'km_iniziali' => 'integer',
        'km_finali' => 'integer',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
