<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalEmployee extends Model
{
    use HasFactory;

    protected $table = 'internal_employees';

    protected $fillable = [
        'first_name',
        'last_name',
        'tax_code',
        'birth_date',
        'birth_place',
        'gender',
        'badge_number',
        'position',
        'employee_type',
        'status',
        'email',
        'personal_email',
        'phone',
        'personal_phone',
        'is_aib_qualified',
        'is_emergency_available',
        'operational_roles',
        'organization_id',
        'contract_id',
        'level_id',
        'location_id',
        'notes'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_aib_qualified' => 'boolean',
        'is_emergency_available' => 'boolean',
        'operational_roles' => 'array',
    ];

    /**
     * Get the user that represents this employee.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
