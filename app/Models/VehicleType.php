<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    protected $fillable = [
        'name',
        'documentazione',
        'certificazioni',
        'patente',
        'revisione',
        'assicurazione',
        'tipo_abilitazione',
        'ente_controllo'
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}
