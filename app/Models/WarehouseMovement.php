<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseMovement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'movement_date',
        'user_id',
        'movement_type',
        'source_location_id',
        'destination_location_id',
        'product_id',
        'quantity',
        'notes',
        'assigned_to_user_id',
    ];

    protected $casts = [
        'movement_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sourceLocation()
    {
        return $this->belongsTo(WarehouseLocation::class, 'source_location_id');
    }

    public function destinationLocation()
    {
        return $this->belongsTo(WarehouseLocation::class, 'destination_location_id');
    }

    public function product()
    {
        return $this->belongsTo(WarehouseProduct::class, 'product_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(InternalEmployee::class, 'assigned_to_user_id');
    }
}
