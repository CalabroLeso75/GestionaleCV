<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DashboardSection extends Model
{
    protected $fillable = [
        'title',
        'description',
        'icon',
        'route',
        'color',
        'required_role',
        'required_area',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get sections visible to a specific user.
     * Checks both Spatie roles (required_role) and area assignments (required_area).
     */
    public static function visibleTo($user)
    {
        $query = static::where('is_active', 1);

        // Fetch all active sections
        $sections = $query->orderBy('sort_order')->get();

        // If super-admin or specific admin user, skip further filtering
        if ($user->hasRole('super-admin') || $user->email === 'raffaele.cusano@calabriaverde.eu') {
            return $sections;
        }

        return $sections->filter(function ($section) use ($user) {
            // If super-admin or specific email, everything is visible
            if ($user->hasRole('super-admin') || $user->email === 'raffaele.cusano@calabriaverde.eu') {
                return true;
            }

            // Check required Spatie role
            if (!empty($section->required_role) && !$user->hasRole($section->required_role)) {
                return false;
            }

            // Check required area
            if (!empty($section->required_area)) {
                $hasArea = DB::table('user_area_roles')
                    ->where('user_id', $user->id)
                    ->where('area', $section->required_area)
                    ->exists();
                
                if (!$hasArea) return false;
            }

            return true;
        });
    }
}
