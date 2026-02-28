<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatorCertification extends Model
{
    protected $fillable = [
        'user_id',
        'tipo',
        'documento',
        'scadenza',
        'file_path'
    ];

    protected $casts = [
        'scadenza' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
