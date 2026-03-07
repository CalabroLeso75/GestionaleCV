<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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

    protected static function booted()
    {
        static::saving(function ($vehicle) {
            $hasValidInsurance = $vehicle->scadenza_assicurazione && Carbon::parse($vehicle->scadenza_assicurazione)->addDays(15)->isFuture();
            $hasValidRevision = $vehicle->scadenza_revisione && Carbon::parse($vehicle->scadenza_revisione)->isFuture();

            // Solo se lo stato attuale è manca copertura, operativo o vuoto
            if (in_array($vehicle->stato, ['operativo', 'manca copertura assicurativa o revisione', 'manca copertura', null])) {
                if ($hasValidInsurance && $hasValidRevision) {
                    $vehicle->stato = 'operativo';
                } else {
                    $vehicle->stato = 'manca copertura assicurativa o revisione';
                }
            }
        });
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function revisions()
    {
        return $this->hasMany(VehicleRevision::class);
    }

    public function teamVehicles()
    {
        return $this->hasMany(AibTeamVehicle::class);
    }
    
    /**
     * Get all of the vehicle's resource assignments.
     */
    public function resourceAssignments()
    {
        return $this->morphMany(ResourceAssignment::class, 'assignable');
    }

    /**
     * Get the currently active resource assignment.
     */
    public function activeAssignment()
    {
        return $this->morphOne(ResourceAssignment::class, 'assignable')->whereNull('data_restituzione');
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
