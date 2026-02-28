<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAreaRole extends Model
{
    protected $table = 'user_area_roles';

    protected $fillable = [
        'user_id',
        'role',
        'area',
        'privilege_level',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
