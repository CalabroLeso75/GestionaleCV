<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email_address',
        'role_type', // e.g. 'soup', 'cop', 'test'
        'is_active',
        'province',
        'municipality',
        'notes'
    ];

    /**
     * Get active emails by role type.
     */
    public static function getActiveEmailsByRole($roleType, $province = null)
    {
        $query = self::where('role_type', $roleType)->where('is_active', true);
        
        if ($province) {
            $query->where(function($q) use ($province) {
                $q->where('province', $province)
                  ->orWhereNull('province')
                  ->orWhere('province', '');
            });
        }
        
        return $query->pluck('email_address')->toArray();
    }
}
