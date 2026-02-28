<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AibTeamMember extends Model
{
    use HasFactory;

    protected $table = 'aib_team_members';

    protected $fillable = [
        'team_id',
        'member_id',
        'member_type',
        'is_caposquadra',
        'is_autista',
        'ruolo_specifico'
    ];

    public function team()
    {
        return $this->belongsTo(AibTeam::class, 'team_id');
    }

    public function member()
    {
        return $this->morphTo();
    }
}
