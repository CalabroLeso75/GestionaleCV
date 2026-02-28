<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'codice_incidente',
        'pcm_incidente_id',
        'data_ora',
        'comune_id',
        'indirizzo',
        'lat',
        'lon',
        'tipo_evento',
        'priorita',
        'stato',
        'descrizione',
        'segnalatore',
        'telefono_segnalatore',
    ];

    public function comune()
    {
        return $this->belongsTo(\App\Models\Location\City::class, 'comune_id');
    }

    /**
     * Generate unique incident code: COR-YYYY-XXX
     */
    public static function generateCode()
    {
        $year = date('Y');
        $lastIncident = self::whereYear('data_ora', $year)->latest()->first();
        if ($lastIncident) {
            $parts = explode('-', $lastIncident->codice_incidente);
            $sequence = (int)end($parts) + 1;
        } else {
            $sequence = 1;
        }
        return sprintf("COR-%s-%03d", $year, $sequence);
    }
}
