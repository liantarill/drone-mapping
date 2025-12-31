<?php

namespace App\Http\Controllers;

use App\Models\Mission;
use App\Models\MissionExport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class MissionExportController extends Controller
{
    /**
     * Export mission to KMZ format.
     */
    public function export(Mission $mission): JsonResponse
    {
        try {
            $mission->load(['waypoints.actions']);

            // Validate mission has waypoints
            if ($mission->waypoints->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mission has no waypoints to export',
                ], 422);
            }

            // Generate KML content
            $kmlContent = $this->generateKML($mission);

            // Create KMZ file (zipped KML)
            $filename = 'mission_'.$mission->id.'_'.time().'.kmz';
            $tempPath = storage_path('app/temp');

            if (! file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }

            $kmlPath = $tempPath.'/doc.kml';
            $kmzPath = $tempPath.'/'.$filename;

            // Write KML file
            file_put_contents($kmlPath, $kmlContent);

            // Create KMZ (zip) file
            $zip = new ZipArchive;
            if ($zip->open($kmzPath, ZipArchive::CREATE) === true) {
                $zip->addFile($kmlPath, 'doc.kml');
                $zip->close();
            }

            // Move to storage
            $storagePath = 'exports/'.$filename;
            Storage::put($storagePath, file_get_contents($kmzPath));

            // Clean up temp files
            unlink($kmlPath);
            unlink($kmzPath);

            // Create export record
            $export = MissionExport::create([
                'mission_id' => $mission->id,
                'status' => 'success',
                'kmz_path' => $storagePath,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mission exported successfully',
                'data' => [
                    'export' => $export,
                    'download_url' => Storage::url($storagePath),
                ],
            ]);

        } catch (\Exception $e) {
            // Create failed export record
            MissionExport::create([
                'mission_id' => $mission->id,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to export mission',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate KML content for the mission.
     */
    private function generateKML(Mission $mission): string
    {
        $waypoints = $mission->waypoints->sortBy('sequence');

        $kml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $kml .= '<kml xmlns="http://www.opengis.net/kml/2.2">'."\n";
        $kml .= '  <Document>'."\n";
        $kml .= '    <name>'.htmlspecialchars($mission->name).'</name>'."\n";
        $kml .= '    <description>Mission ID: '.$mission->id.'</description>'."\n";

        // Add mission metadata
        $kml .= '    <ExtendedData>'."\n";
        $kml .= '      <Data name="finish_action"><value>'.$mission->finish_action.'</value></Data>'."\n";
        $kml .= '      <Data name="rc_lost_action"><value>'.$mission->rc_lost_action.'</value></Data>'."\n";
        $kml .= '      <Data name="global_speed"><value>'.$mission->global_speed.'</value></Data>'."\n";

        if ($mission->takeoff_lat && $mission->takeoff_lng) {
            $kml .= '      <Data name="takeoff_lat"><value>'.$mission->takeoff_lat.'</value></Data>'."\n";
            $kml .= '      <Data name="takeoff_lng"><value>'.$mission->takeoff_lng.'</value></Data>'."\n";
            $kml .= '      <Data name="takeoff_alt"><value>'.$mission->takeoff_alt.'</value></Data>'."\n";
        }

        $kml .= '    </ExtendedData>'."\n";

        // Add path line
        $kml .= '    <Placemark>'."\n";
        $kml .= '      <name>Flight Path</name>'."\n";
        $kml .= '      <LineString>'."\n";
        $kml .= '        <altitudeMode>absolute</altitudeMode>'."\n";
        $kml .= '        <coordinates>'."\n";

        foreach ($waypoints as $waypoint) {
            $kml .= '          '.$waypoint->longitude.','.$waypoint->latitude.','.$waypoint->altitude."\n";
        }

        $kml .= '        </coordinates>'."\n";
        $kml .= '      </LineString>'."\n";
        $kml .= '    </Placemark>'."\n";

        // Add waypoints as placemarks
        foreach ($waypoints as $waypoint) {
            $kml .= '    <Placemark>'."\n";
            $kml .= '      <name>WP'.$waypoint->sequence.'</name>'."\n";
            $kml .= '      <description>'."\n";
            $kml .= '        Sequence: '.$waypoint->sequence."\n";
            $kml .= '        Altitude: '.$waypoint->altitude.'m'."\n";
            $kml .= '        Speed: '.($waypoint->speed ?? $mission->global_speed).'m/s'."\n";
            $kml .= '        Heading Mode: '.$waypoint->heading_mode."\n";

            if ($waypoint->heading !== null) {
                $kml .= '        Heading: '.$waypoint->heading.'°'."\n";
            }

            if ($waypoint->gimbal_pitch !== null) {
                $kml .= '        Gimbal Pitch: '.$waypoint->gimbal_pitch.'°'."\n";
            }

            if ($waypoint->actions->isNotEmpty()) {
                $kml .= '        Actions: '.$waypoint->actions->pluck('action_type')->implode(', ')."\n";
            }

            $kml .= '      </description>'."\n";
            $kml .= '      <Point>'."\n";
            $kml .= '        <altitudeMode>absolute</altitudeMode>'."\n";
            $kml .= '        <coordinates>'.$waypoint->longitude.','.$waypoint->latitude.','.$waypoint->altitude.'</coordinates>'."\n";
            $kml .= '      </Point>'."\n";
            $kml .= '    </Placemark>'."\n";
        }

        $kml .= '  </Document>'."\n";
        $kml .= '</kml>';

        return $kml;
    }
}
