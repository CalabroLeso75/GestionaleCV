<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'localizz_regione';

    protected $fillable = ['name'];

    public function provinces()
    {
        return $this->hasMany(Province::class, 'region_id');
    }
}
