<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role_snapshot',
        'op_lat',
        'op_lng',
        'fire_lat',
        'fire_lng',
        'distance',
        'municipality',
        'province',
        'toponym',
        'temperature',
        'wind_speed',
        'wind_direction',
        'wind_forecast_2h_speed',
        'wind_forecast_2h_dir',
        'wind_forecast_2h_gust',
        'wind_forecast_4h_speed',
        'wind_forecast_4h_dir',
        'wind_forecast_4h_gust',
        'wind_forecast_6h_speed',
        'wind_forecast_6h_dir',
        'wind_forecast_6h_gust',
        'notes',
        'polygon_geojson',
        'area_hectares',
        'front_meters',
        'kml_path',
    ];

    protected $casts = [
        'op_lat' => 'float',
        'op_lng' => 'float',
        'fire_lat' => 'float',
        'fire_lng' => 'float',
        'area_hectares' => 'float',
        'front_meters' => 'integer',
        'temperature' => 'float',
        'wind_speed' => 'float',
        'wind_forecast_2h_speed' => 'float',
        'wind_forecast_4h_speed' => 'float',
        'wind_forecast_6h_speed' => 'float',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
