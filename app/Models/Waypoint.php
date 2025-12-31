<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Waypoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'mission_id',
        'sequence',
        'latitude',
        'longitude',
        'altitude',
        'speed',
        'heading_mode',
        'heading',
        'gimbal_pitch',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'altitude' => 'float',
        'speed' => 'float',
        'heading' => 'integer',
        'gimbal_pitch' => 'integer',
    ];

    /* =====================
     |  RELATIONS
     |=====================*/
    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }

    public function actions()
    {
        return $this->hasMany(WaypointAction::class);
    }

    /* =====================
     |  HELPERS
     |=====================*/

    // Format koordinat sesuai WPML (lng,lat,alt)
    public function wpmlCoordinates(): string
    {
        return "{$this->longitude},{$this->latitude},{$this->altitude}";
    }
}
