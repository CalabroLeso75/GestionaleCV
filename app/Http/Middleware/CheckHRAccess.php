<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckHRAccess
{
    /**
     * Check if user has access to the HR section.
     * Access granted if:
     *   - User is super-admin
     *   - User has any record in user_area_roles with area = 'Risorse Umane'
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Super-admin always has access
        if ($user->hasRole('super-admin')) {
            return $next($request);
        }

        // Check if user has any HR area assignment
        $hasHRAccess = DB::table('user_area_roles')
            ->where('user_id', $user->id)
            ->where('area', 'Risorse Umane')
            ->exists();

        if (!$hasHRAccess) {
            abort(403, 'Non hai i permessi per accedere alla sezione Risorse Umane.');
        }

        return $next($request);
    }
}
