<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'surname',
        'gender',
        'email',
        'password',
        'fiscal_code',
        'birth_date',
        'birth_city_id',
        'birth_country_id',
        'internal_employee_id',
        'type',
        'status',
        'otp_code',
        'otp_expires_at',
    ];

    public function birthCity()
    {
        return $this->belongsTo(\App\Models\Location\City::class, 'birth_city_id');
    }

    public function birthCountry()
    {
        return $this->belongsTo(\App\Models\Location\Country::class, 'birth_country_id');
    }

    public function internalEmployee()
    {
        return $this->belongsTo(\App\Models\InternalEmployee::class, 'internal_employee_id');
    }

    /**
     * Get the area roles and privilege levels assigned to the user.
     */
    public function areaRoles()
    {
        return $this->hasMany(\App\Models\UserAreaRole::class, 'user_id');
    }

    /**
     * Direct warehouse locations assigned to the user.
     */
    public function warehouseLocations()
    {
        return $this->belongsToMany(WarehouseLocation::class, 'user_warehouse_locations')->withTimestamps();
    }

    /**
     * Helper to get all managed warehouse locations (including children).
     */
    public function getAllManagedLocations()
    {
        if ($this->hasRole('super-admin') || $this->email === 'raffaele.cusano@calabriaverde.eu') {
            return WarehouseLocation::all();
        }

        $directLocations = $this->warehouseLocations;
        $allLocations = collect();

        foreach ($directLocations as $loc) {
            $allLocations->push($loc);
            // Recursively get children if needed, or just 1 level for now
            // Assumiamo max 2-3 livelli (Distretto -> Magazzino)
            $allLocations = $allLocations->merge($loc->children);
            foreach ($loc->children as $child) {
                $allLocations = $allLocations->merge($child->children);
            }
        }

        return $allLocations->unique('id')->values();
    }

    /**
     * Send the password reset notification in Italian.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function certifications()
    {
        return $this->hasMany(\App\Models\OperatorCertification::class);
    }
}
