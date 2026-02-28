<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalEmployee extends Model
{
    use HasFactory;

    protected $table = 'external_employees';

    protected $fillable = [
        'organization_id',
        'first_name',
        'last_name',
        'tax_code',
        'birth_date',
        'birth_city_id',
        'birth_place_text',
        'job_title',
        'is_aib',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_aib' => 'boolean',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    
    public function birthCity()
    {
        return $this->belongsTo(Location\City::class, 'birth_city_id');
    }
}
