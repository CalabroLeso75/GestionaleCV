<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $table = 'localizz_provincia';

    protected $fillable = ['name', 'short_code'];

    // Region relationship removed as column 'region_id' does not exist in DB
    /*
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }
    */

    public function cities()
    {
        return $this->hasMany(City::class, 'province_id');
    }
}
