<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamAssetLog extends Model
{
    use HasFactory;

    protected $table = 'team_asset_logs';

    protected $fillable = [
        'team_id',
        'asset_type',
        'asset_name',
        'action',
        'old_assignees',
        'new_assignees',
        'user_id'
    ];

    protected $casts = [
        'old_assignees' => 'array',
        'new_assignees' => 'array',
    ];

    public function team()
    {
        return $this->belongsTo(AibTeam::class, 'team_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
