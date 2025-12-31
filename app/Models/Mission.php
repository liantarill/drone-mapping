<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'drone_model',
        'fly_to_wayline_mode',
        'finish_action',
        'rc_lost_action',
        'global_speed',
        'takeoff_lat',
        'takeoff_lng',
        'takeoff_alt',
    ];

    protected $casts = [
        'global_speed' => 'float',
        'takeoff_lat' => 'float',
        'takeoff_lng' => 'float',
        'takeoff_alt' => 'float',
    ];

    /* =====================
     |  RELATIONS
     |=====================*/
    public function waypoints()
    {
        return $this->hasMany(Waypoint::class)
            ->orderBy('sequence');
    }

    public function exports()
    {
        return $this->hasMany(MissionExport::class);
    }

    /* =====================
     |  HELPERS (OPTIONAL)
     |=====================*/
    public function getWpmlMissionConfig(): array
    {
        return [
            'flyToWaylineMode' => $this->fly_to_wayline_mode,
            'finishAction' => $this->finish_action,
            'exitOnRCLost' => $this->rc_lost_action,
            'globalSpeed' => $this->global_speed,
        ];
    }
}
