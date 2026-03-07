<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DashboardSection extends Model
{
    protected $fillable = [
        'parent_id',
        'level',
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
            'level'     => 'integer',
        ];
    }

    /**
     * Role hierarchy: a role at level N can see sections requiring any role at level <= N.
     * Higher number = more privileges.
     */
    const ROLE_HIERARCHY = [
        'employee'                    => 1,
        'operatore parco macchine'    => 2,
        'responsabile parco macchine' => 2,
        'hr-manager'                  => 2,
        'pc'                          => 2,
        'admin'                       => 8,
        'amministratore di sistema'   => 8,
        'super-admin'                 => 10,
    ];

    // ── Relations ──────────────────────────────────────────────────────

    public function parent()
    {
        return $this->belongsTo(DashboardSection::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(DashboardSection::class, 'parent_id')->orderBy('sort_order');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    /** Roles that have been explicitly granted this section as a "package". */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_sections', 'dashboard_section_id', 'role_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id')->orderBy('sort_order');
    }

    // ── Helpers ────────────────────────────────────────────────────────

    public function levelLabel(): string
    {
        return match ($this->level) {
            1 => 'Modulo',
            2 => 'Sezione',
            3 => 'Sotto-sezione',
            4 => 'Pagina',
            default => 'Livello ' . $this->level,
        };
    }

    public function levelBadgeClass(): string
    {
        return match ($this->level) {
            1 => 'primary',
            2 => 'success',
            3 => 'warning',
            4 => 'secondary',
            default => 'dark',
        };
    }

    // ── Visibility ──────────────────────────────────────────────────────

    /**
     * All active sections visible to the given user.
     */
    public static function visibleTo($user): \Illuminate\Support\Collection
    {
        $all = static::where('is_active', 1)->orderBy('sort_order')->get();

        // Super-admin sees EVERYTHING
        if (static::isSuperAdmin($user)) {
            return $all;
        }

        // Compute the user's maximum hierarchy level across all their Spatie roles
        $userMaxLevel = 0;
        foreach ($user->getRoleNames() as $roleName) {
            $rLevel = static::ROLE_HIERARCHY[strtolower($roleName)] ?? 0;
            if ($rLevel > $userMaxLevel) $userMaxLevel = $rLevel;
        }

        // The set of section IDs explicitly assigned to any of the user's roles
        $assignedSectionIds = DB::table('role_sections')
            ->join('roles', 'roles.id', '=', 'role_sections.role_id')
            ->whereIn('roles.name', $user->getRoleNames()->toArray())
            ->pluck('role_sections.dashboard_section_id')
            ->toArray();

        return $all->filter(function ($section) use ($user, $userMaxLevel, $assignedSectionIds) {
            return static::userCanSee($section, $user, $userMaxLevel, $assignedSectionIds);
        })->values();
    }

    /** L1 sections visible to user (for main dashboard tiles). */
    public static function rootsVisibleTo($user): \Illuminate\Support\Collection
    {
        return static::visibleTo($user)->where('level', 1)->values();
    }

    /**
     * L2/L3/L4 sections visible to user, grouped by their L1 ancestor title.
     */
    public static function deepLinksVisibleTo($user): \Illuminate\Support\Collection
    {
        $all = static::visibleTo($user)->where('level', '>', 1);
        if ($all->isEmpty()) return collect();

        $allById = static::all()->keyBy('id');

        return $all->groupBy(function ($s) use ($allById) {
            $current = $s;
            while ($current->parent_id && isset($allById[$current->parent_id])) {
                $current = $allById[$current->parent_id];
            }
            return $current->title;
        });
    }

    // ── Private helpers ─────────────────────────────────────────────────

    public static function isSuperAdmin($user): bool
    {
        return $user->hasRole('super-admin')
            || $user->email === 'raffaele.cusano@calabriaverde.eu';
    }

    private static function userCanSee($section, $user, int $userMaxLevel, array $assignedSectionIds): bool
    {
        // 1. Check required_role using hierarchy
        if (!empty($section->required_role)) {
            $requiredLevel = static::ROLE_HIERARCHY[strtolower($section->required_role)] ?? 99;
            // Only super-admin (level 10) sees sections requiring 'super-admin' (level 10)
            if ($userMaxLevel < $requiredLevel) {
                return false;
            }
        }

        // 2. Check required_area in user_area_roles
        if (!empty($section->required_area)) {
            $hasArea = DB::table('user_area_roles')
                ->where('user_id', $user->id)
                ->whereRaw('LOWER(area) = ?', [strtolower($section->required_area)])
                ->exists();
            if (!$hasArea) return false;
        }

        // 3. If this section is explicitly assigned to one of the user's roles, always show it
        if (in_array($section->id, $assignedSectionIds)) {
            return true;
        }

        // 4. If no restrictions apply (no required_role and no required_area), visible to all
        if (empty($section->required_role) && empty($section->required_area)) {
            return true;
        }

        // 5. Both checks passed
        return true;
    }
}
