<?php

namespace App\Http\Controllers;

use App\Models\Mission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MissionController extends Controller
{
    /**
     * Display a listing of missions.
     */
    public function index(): JsonResponse
    {
        $missions = Mission::with(['waypoints.actions'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $missions,
        ]);
    }

    /**
     * Store a newly created mission.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'finish_action' => 'required|in:goHome,land,hover',
            'rc_lost_action' => 'required|in:goHome,land,hover',
            'global_speed' => 'required|numeric|between:0,99.99',
            'takeoff_lat' => 'nullable|numeric|between:-90,90',
            'takeoff_lng' => 'nullable|numeric|between:-180,180',
            'takeoff_alt' => 'nullable|numeric|min:0',
        ]);

        $mission = Mission::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Mission created successfully',
            'data' => $mission,
        ], 201);
    }

    /**
     * Display the specified mission.
     */
    public function show(Mission $mission): JsonResponse
    {
        $mission->load(['waypoints.actions']);

        return response()->json([
            'success' => true,
            'data' => $mission,
        ]);
    }

    /**
     * Update the specified mission.
     */
    public function update(Request $request, Mission $mission): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'finish_action' => 'sometimes|required|in:goHome,land,hover',
            'rc_lost_action' => 'sometimes|required|in:goHome,land,hover',
            'global_speed' => 'sometimes|required|numeric|between:0,99.99',
            'takeoff_lat' => 'nullable|numeric|between:-90,90',
            'takeoff_lng' => 'nullable|numeric|between:-180,180',
            'takeoff_alt' => 'nullable|numeric|min:0',
        ]);

        $mission->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Mission updated successfully',
            'data' => $mission->fresh(),
        ]);
    }

    /**
     * Remove the specified mission.
     */
    public function destroy(Mission $mission): JsonResponse
    {
        $mission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mission deleted successfully',
        ]);
    }
}
