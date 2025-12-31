<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MissionExport extends Model
{
    use HasFactory;

    protected $fillable = [
        'mission_id',
        'status',
        'kmz_path',
        'error_message',
    ];

    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }
}
