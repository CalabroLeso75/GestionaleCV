<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'targa',
        'marca',
        'modello',
        'tipo',
        'immatricolazione_date',
        'immatricolazione_mese',
        'immatricolazione_anno',
        'assicurazione_compagnia',
        'assicurazione_polizza',
        'scadenza_assicurazione',
        'assicurazione_copertura',
        'scadenza_revisione',
        'rottamazione_date',
        'km_attuali',
        'stato',
        'ultima_revisione',
        'vehicle_type_id',
        'scadenza_verifica_sicurezza'
    ];

    protected $casts = [
        'immatricolazione_date' => 'date',
        'scadenza_assicurazione' => 'date',
        'assicurazione_copertura' => 'date',
        'scadenza_revisione' => 'date',
        'rottamazione_date' => 'date',
        'km_attuali' => 'integer',
        'immatricolazione_mese' => 'integer',
        'updated_at' => 'datetime',
        'ultima_revisione' => 'date',
        'scadenza_verifica_sicurezza' => 'date'
    ];

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function revisions()
    {
        return $this->hasMany(VehicleRevision::class);
    }

    public function logs()
    {
        return $this->hasMany(VehicleLog::class);
    }

    public function currentLog()
    {
        return $this->hasOne(VehicleLog::class)->whereNull('riconsegnato_il');
    }

    public function calculateRevisionExpiry($lastRevisionDate = null)
    {
        if ($lastRevisionDate) {
            $date = \Carbon\Carbon::parse($lastRevisionDate);
            // End of month after 2 years
            return $date->addYears(2)->endOfMonth()->toDateString();
        }

        if ($this->immatricolazione_date) {
            $date = \Carbon\Carbon::parse($this->immatricolazione_date);
            // End of month after 4 years
            return $date->addYears(4)->endOfMonth()->toDateString();
        }

        if ($this->immatricolazione_anno && $this->immatricolazione_mese) {
            $date = \Carbon\Carbon::createFromDate($this->immatricolazione_anno, $this->immatricolazione_mese, 1);
            // End of month after 4 years
            return $date->addYears(4)->endOfMonth()->toDateString();
        }

        return null;
    }
}
