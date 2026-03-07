<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MobileDevice extends Model
{
    use HasFactory;

    protected $table = 'mobile_devices';

    protected $fillable = [
        'marca',
        'modello',
        'tipo',
        'colore',
        'anno_acquisto',
        'numero_telefono',
        'imei',
        'seriale',
        'asset_code',
        'stato',
        // Specifiche tecniche (facoltative)
        'sistema_operativo',
        'versione_os',
        'dimensione_schermo',
        'memoria_ram',
        'memoria_storage',
        'processore',
        'fotocamera_principale',
        '5g',
        'nfc',
        'batteria_mah',
        'note',
    ];

    public function teams()
    {
        return $this->belongsToMany(AibTeam::class, 'aib_team_mobile_devices', 'mobile_device_id', 'team_id');
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
