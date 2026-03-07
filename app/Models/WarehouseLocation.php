<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'parent_id',
    ];

    /**
     * The parent location (e.g. this is a Distretto, parent is Sede Centrale).
     */
    public function parent()
    {
        return $this->belongsTo(WarehouseLocation::class, 'parent_id');
    }

    /**
     * The child locations (e.g. this is a Distretto, children are HUB and Magazzini).
     */
    public function children()
    {
        return $this->hasMany(WarehouseLocation::class, 'parent_id');
    }

    /**
     * Users assigned to this location.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_warehouse_locations')
                    ->withTimestamps();
    }
}
