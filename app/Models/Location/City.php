<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'localizz_comune';

    // Disable timestamps if table doesn't have them or uses different names
    // SQL dump had created_at/updated_at, so we keep defaults usually.
    
    protected $fillable = ['name', 'province_id', 'cadastral_code'];

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }
}
