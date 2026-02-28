<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $table = 'organizations';

    protected $fillable = [
        'name',
        'type', // public, private
        'tax_code',
        'vat_number',
        'email',
        'phone',
        'city_id',
        'address',
        'is_aib'
    ];

    protected $casts = [
        'is_aib' => 'boolean',
    ];

    public function city()
    {
        return $this->belongsTo(Location\City::class, 'city_id');
    }

    public function externalEmployees()
    {
        return $this->hasMany(ExternalEmployee::class);
    }
}
