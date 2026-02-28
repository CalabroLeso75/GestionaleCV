<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyPhone extends Model
{
    protected $fillable = [
        'numero',
        'alias',
        'imei',
        'operatore',
        'piano_telefonico',
        'stato'
    ];
}
