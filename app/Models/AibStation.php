<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AibStation extends Model
{
    use HasFactory;

    protected $table = 'aib_stations';

    protected $fillable = [
        'nome',
        'slug',
        'location_id',
        'provincia',
        'comune',
        'localita',
        'latitudine',
        'longitudine',
        'lat_dms',
        'lon_dms',
        'tipo',
        'stato',
        'note'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($station) {
            if (!$station->slug) {
                $station->slug = Str::slug($station->nome);
            }
        });
    }

    /**
     * Scope for a specific province.
     */
    public function scopeOfProvince($query, $province)
    {
        return $query->where('provincia', $province);
    }
}
