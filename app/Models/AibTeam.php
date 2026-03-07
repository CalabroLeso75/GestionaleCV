<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AibTeam extends Model
{
    use HasFactory;

    protected $table = 'aib_teams';

    protected $fillable = [
        'sigla',
        'data_inizio',
        'data_fine',
        'campagna',
        'turno',
        'stato_operativo',
        'note'
    ];

    protected $casts = [
        'data_inizio' => 'date',
        'data_fine' => 'date',
    ];

    public function stations()
    {
        return $this->belongsToMany(AibStation::class, 'aib_team_stations', 'team_id', 'station_id');
    }

    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'aib_team_vehicles', 'team_id', 'vehicle_id');
    }

    public function phones()
    {
        return $this->belongsToMany(CompanyPhone::class, 'aib_team_phones', 'team_id', 'phone_id');
    }

    public function members()
    {
        return $this->hasMany(AibTeamMember::class, 'team_id');
    }
    public function mobileDevices()
    {
        return $this->belongsToMany(MobileDevice::class, 'aib_team_mobile_devices', 'team_id', 'mobile_device_id');
    }

    public function assetLogs()
    {
        return $this->hasMany(TeamAssetLog::class, 'team_id');
    }
    

}
