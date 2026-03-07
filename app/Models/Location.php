<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'locations';

    protected $fillable = [
        'nome',
        'tipo_sede',
        'indirizzo',
        'citta',
        'localita',
        'provincia',
        'cap',
        'lat',
        'lng',
        'note_operative',
        'is_active',
    ];

    protected $casts = [
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    /**
     * Get the AIB stations associated with this location.
     */
    public function aibStations()
    {
        return $this->hasMany(AibStation::class);
    }
}
