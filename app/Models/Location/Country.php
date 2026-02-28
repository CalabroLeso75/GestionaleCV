<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'localizz_statoestero';

    protected $fillable = ['name_it', 'iso2', 'cadastral_code'];
}
