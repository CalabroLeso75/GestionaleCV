<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'data_revisione',
        'esito',
        'km_rilevati',
        'note',
    ];

    protected $casts = [
        'data_revisione' => 'date',
        'km_rilevati' => 'integer',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
