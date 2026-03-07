<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'barcode',
        'name',
        'brand',
        'category',
        'unit_of_measure',
        'is_inventariable',
    ];

    protected $casts = [
        'is_inventariable' => 'boolean',
    ];

}
