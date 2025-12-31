<?php

namespace App\Http\Controllers;

use App\Models\Waypoint;
use App\Models\WaypointAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WaypointActionController extends Controller
{
    /**
     * Store a newly created action for a waypoint.
     */
    public function store(Request $request, Waypoint $waypoint): JsonResponse
    {
        $validated = $request->validate([
            'action_type' => 'required|in:takePhoto,startRecord,stopRecord,rotateAircraft,gimbalPitch',
            'params' => 'nullable|array',
        ]);

        // Validate params based on action type
        $this->validateActionParams($validated['action_type'], $validated['params'] ?? []);

        $validated['waypoint_id'] = $waypoint->id;

        $action = WaypointAction::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Action created successfully',
            'data' => $action,
        ], 201);
    }

    /**
     * Update the specified action.
     */
    public function update(Request $request, WaypointAction $action): JsonResponse
    {
        $validated = $request->validate([
            'action_type' => 'sometimes|required|in:takePhoto,startRecord,stopRecord,rotateAircraft,gimbalPitch',
            'params' => 'nullable|array',
        ]);

        // Validate params based on action type
        if (isset($validated['action_type'])) {
            $this->validateActionParams($validated['action_type'], $validated['params'] ?? []);
        }

        $action->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Action updated successfully',
            'data' => $action->fresh(),
        ]);
    }

    /**
     * Remove the specified action.
     */
    public function destroy(WaypointAction $action): JsonResponse
    {
        $action->delete();

        return response()->json([
            'success' => true,
            'message' => 'Action deleted successfully',
        ]);
    }

    /**
     * Validate action parameters based on action type.
     */
    private function validateActionParams(string $actionType, array $params): void
    {
        switch ($actionType) {
            case 'rotateAircraft':
                if (! isset($params['heading']) || ! is_numeric($params['heading'])) {
                    abort(422, 'rotateAircraft requires heading parameter (0-359)');
                }
                if ($params['heading'] < 0 || $params['heading'] > 359) {
                    abort(422, 'heading must be between 0 and 359');
                }
                break;

            case 'gimbalPitch':
                if (! isset($params['pitch']) || ! is_numeric($params['pitch'])) {
                    abort(422, 'gimbalPitch requires pitch parameter (-90 to 30)');
                }
                if ($params['pitch'] < -90 || $params['pitch'] > 30) {
                    abort(422, 'pitch must be between -90 and 30');
                }
                break;

            case 'takePhoto':
            case 'startRecord':
            case 'stopRecord':
                // These actions typically don't require additional params
                break;
        }
    }
}
