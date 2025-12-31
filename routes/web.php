<?php

use App\Http\Controllers\MissionController;
use App\Http\Controllers\MissionExportController;
use App\Http\Controllers\WaypointActionController;
use App\Http\Controllers\WaypointController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return view('welcome');

    // return redirect()->route('missions.index');
});

Route::apiResource('missions', MissionController::class);

Route::post('missions/{mission}/waypoints', [WaypointController::class, 'store']);
Route::put('waypoints/{waypoint}', [WaypointController::class, 'update']);
Route::delete('waypoints/{waypoint}', [WaypointController::class, 'destroy']);
Route::post('missions/{mission}/waypoints/reorder', [WaypointController::class, 'reorder']);

Route::post('waypoints/{waypoint}/actions', [WaypointActionController::class, 'store']);
Route::put('actions/{action}', [WaypointActionController::class, 'update']);
Route::delete('actions/{action}', [WaypointActionController::class, 'destroy']);

Route::get('missions/{mission}/export', [MissionExportController::class, 'export']);
