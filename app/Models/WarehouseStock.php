<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'product_id',
        'quantity',
        'min_stock',
        'optimal_stock',
    ];

    public function location()
    {
        return $this->belongsTo(WarehouseLocation::class, 'location_id');
    }

    public function product()
    {
        return $this->belongsTo(WarehouseProduct::class, 'product_id');
    }

    public function isBelowMinStock()
    {
        return $this->min_stock && $this->quantity < $this->min_stock;
    }
}
