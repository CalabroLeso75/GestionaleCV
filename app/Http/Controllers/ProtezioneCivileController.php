<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogger;

class ProtezioneCivileController extends Controller
{
    /**
     * Display the Protezione Civile dashboard with its sub-sections.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Define the group of area slugs for Protezione Civile
        $pcAreaSlugs = [
            'soup', 'cop-cosenza', 'cop-catanzaro', 'cop-crotone', 
            'cop-vibo-valentia', 'cop-reggio-calabria', 'antincendio-boschivo', 
            'emergenze-pc', 'mezzi-aerei', 'squadre-aib-pc', 
            'mezzi-terra', 'utenze-aziendali', 'turnazioni-aib', 'autoparco'
        ];

        // Fetch user permissions for these areas
        $userPermissions = DB::table('user_area_roles')
            ->where('user_id', $user->id)
            ->whereIn('area', array_map(function($slug) use ($pcAreaSlugs) {
                // Since user_area_roles stores area NAMES and not SLUGS (as seen in UserManagementController::getAreas)
                // We need to match by area names exactly or change logic.
                // Looking at system_areas, 'name' is the full label.
                return $slug; // Placeholder - wait, I need to check how they are stored.
            }, $pcAreaSlugs))
            ->get()
            ->groupBy('area');

        // Actually, UserManagementController uses 'name' from system_areas.
        // Let's get the full names for the PC areas.
        $pcAreas = DB::table('system_areas')
            ->whereIn('slug', $pcAreaSlugs)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // Check assigned roles for each area
        $assignedAreas = [];
        $copAreas = [];
        $isAdmin = $user->hasRole('super-admin') || $user->email === 'raffaele.cusano@calabriaverde.eu';

        foreach ($pcAreas as $area) {
            // Check by name or by slug in user_area_roles
            $permission = DB::table('user_area_roles')
                ->where('user_id', $user->id)
                ->where(function($query) use ($area) {
                    $query->where('area', $area->name)
                          ->orWhere('area', $area->slug);
                })
                ->first();

            if ($isAdmin || $permission) {
                $level = $isAdmin ? 1 : ($permission->privilege_level ?? 4);
                
                $areaMetadata = $this->getAreaMetadata($area->slug);
                
                $areaData = [
                    'id' => $area->id,
                    'name' => $area->name,
                    'slug' => $area->slug,
                    'description' => $area->description,
                    'role' => $isAdmin ? 'Super Admin' : ($permission->role ?? 'Operatore'),
                    'privilege_level' => $level,
                    'privilege_label' => $this->getPrivilegeLabel($level),
                    'icon' => $areaMetadata['icon'],
                    'url' => $areaMetadata['url'],
                    'color' => $this->getColorForArea($area->slug),
                    'active' => false,
                ];


                // Group COPs separately
                if (strpos($area->slug, 'cop-') === 0) {
                    $copAreas[] = $areaData;
                } else {
                    $assignedAreas[] = $areaData;
                }
            }
        }

        // Consolidate COPs if any are present
        if (!empty($copAreas)) {
            // Find the most permissive level (highest is 1, lowest is 5)
            $minLevel = 5;
            foreach ($copAreas as $cop) {
                if ($cop['privilege_level'] < $minLevel) {
                    $minLevel = $cop['privilege_level'];
                }
            }

            $assignedAreas[] = [
                'id' => 0,
                'name' => 'COP - Centri Operativi Provinciali',
                'slug' => 'cop',
                'description' => 'Accesso ai Centri Operativi Provinciali (' . count($copAreas) . ' province abilitate).',
                'role' => count($copAreas) > 1 ? 'Multi-Provinciale' : $copAreas[0]['role'],
                'privilege_level' => $minLevel,
                'privilege_label' => $this->getPrivilegeLabel($minLevel),
                'icon' => '🏢',
                'url' => '#',
                'color' => '#8e44ad',

                'active' => false,
                'provinces' => $copAreas // Passed for sub-filtering
            ];
        }

        ActivityLogger::log('view', null, null, "Accesso alla sezione Gestione Emergenze Protezione Civile");

        return view('pc.index', compact('assignedAreas'));
    }

    private function getPrivilegeLabel($level)
    {
        $labels = [
            1 => 'Pieno Controllo',
            2 => 'Controllo Parziale',
            3 => 'Controllo Campi',
            4 => 'Sola Lettura (Full)',
            5 => 'Sola Lettura (Parziale)',
        ];
        return $labels[$level] ?? 'Accesso Base';
    }

    private function getAreaMetadata($slug)
    {
        $icons = [
            'soup' => '🏢',
            'cop-cosenza' => '📍',
            'cop-catanzaro' => '📍',
            'cop-crotone' => '📍',
            'cop-vibo-valentia' => '📍',
            'cop-reggio-calabria' => '📍',
            'antincendio-boschivo' => '🔥',
            'emergenze-pc' => '⚠️',
            'mezzi-aerei' => '🚁',
            'squadre-aib-pc' => '👥',
            'mezzi-terra' => '🚛',
            'utenze-aziendali' => '📞',
            'turnazioni-aib' => '📅',
            'autoparco' => '🚗',
        ];

        $urls = [
            'antincendio-boschivo' => route('pc.aib.locations.index'),
            'squadre-aib-pc' => route('pc.aib.teams.index'),
            'turnazioni-aib' => route('pc.aib.teams.index'),
            'emergenze-pc' => route('pc.emergencies.index'),
            'autoparco' => route('autoparco.index'),
            'utenze-aziendali' => route('pc.aib.phones.index'),
        ];


        return [
            'icon' => $icons[$slug] ?? '📁',
            'url' => $urls[$slug] ?? '#'
        ];
    }


    private function getColorForArea($slug)
    {
        if (str_starts_with($slug, 'cop-')) return '#0066cc';
        if ($slug === 'soup') return '#d32f2f';
        if ($slug === 'antincendio-boschivo') return '#ef6c00';
        if ($slug === 'autoparco') return '#2e7d32';
        return '#455a64';
    }
}

