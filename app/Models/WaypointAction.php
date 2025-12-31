<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaypointAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'waypoint_id',
        'action_type',
        'params',
    ];

    protected $casts = [
        'params' => 'array',
    ];

    /* =====================
     |  RELATIONS
     |=====================*/
    public function waypoint()
    {
        return $this->belongsTo(Waypoint::class);
    }

    /* =====================
     |  HELPERS
     |=====================*/
    public function getWpmlParams(): array
    {
        return $this->params ?? [];
    }
}
