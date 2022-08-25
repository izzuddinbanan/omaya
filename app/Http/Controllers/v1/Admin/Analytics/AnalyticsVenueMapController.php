<?php

namespace App\Http\Controllers\v1\Admin\Analytics;

use App\Http\Controllers\Controller;
use App\Models\OmayaDeviceController;
use App\Models\OmayaLocation;
use App\Models\OmayaVenue;
use Illuminate\Http\Request;

class AnalyticsVenueMapController extends Controller
{
    public function index(){


        $min_date  = getDateStart();
        $locations = OmayaLocation::with('venues')->get();

        return view('v1.admin.analytics.venue_map.index', compact('locations', 'min_date'));
        
    }


    public function data(Request $request)
    {
            
        if ($request->ajax()) {


            foreach (['report_date', 'location', 'venue', 'scanner_type'] as $key => $value) {

                if(!$request->input($value)) 
                return respondAjax("warning", "Please select ". ucfirst(str_replace("_", " ", $value)) ." field.", []);
            }

            unset($key, $value);


            $scanner_type = $request->input('scanner_type') == "all" ? "" : "_" . ($request->input('scanner_type') == "wifi" ? "wifi" : "ble");


            if(!$venue = OmayaVenue::where('venue_uid', $request->input('venue'))->whereNotNull('image')->first()) {

                return respondAjax("info", "The are no image for this venue. Please upload venue image at <b>Management -> Venue module</b>", []);

            }

            $ap = OmayaDeviceController::where('venue_uid', $request->input('venue'))->get();


            $date_start = reportDateStart($request->input('report_date'));
            $date_end   = reportDateEnd($request->input('report_date'));

            $tables     = getMonthlyTable($date_start, $date_end, session('timezone'), 'omaya_report_heatmap');


            $omy_temps = [];
            foreach ($tables as $table) {

                if (!\Schema::hasTable($table)) continue;

                $temp_data = \DB::select("SELECT SQL_CACHE report_date, CONVERT_TZ(report_date, 'UTC', '". session('timezone') ."') AS xreport_date, 
                    DATE_FORMAT(CONVERT_TZ(report_date, 'UTC', '". session('timezone') ."'), '%d  %b %y / %a') as date, 
                    TIME_FORMAT(CONVERT_TZ(report_date, 'UTC', '". session('timezone') ."'), '%l%p') as time, 
                    sum(total{$scanner_type}) as total_count,
                    location_uid,
                    venue_uid,
                    zone_uid,
                    location_name,
                    venue_name,
                    zone_name
                    FROM {$table}
                    WHERE tenant_id = '". session('tenant_id') ."' 
                    AND 
                    (report_date BETWEEN '{$date_start}' AND '{$date_end}') 
                    AND
                    location_uid = '{$request->input('location')}' 
                    AND
                    venue_uid = '{$request->input('venue')}' 
                    GROUP BY report_date, location_uid, venue_uid, zone_uid ORDER BY report_date");
                
                $omy_temps = array_merge($omy_temps, $temp_data);

            }

            if(empty($omy_temps)) {
                
                return respondAjax("info", "The are no record for this date range <br> [{$date_start} - {$date_end}]", $omy_temps);

            }

            unset($tables, $table, $temp_data);

            $report_grouping = [];

            foreach ($omy_temps as $temp) {

                if(empty($temp->zone_uid)) {

                    $report_grouping["venue"]["nozone"][$temp->time] = ["time" => $temp->time, "total" => $temp->total_count];

                }else {

                    $report_grouping["zone"][$temp->zone_uid][$temp->time] = ["time" => $temp->time, "total" => $temp->total_count];

                }



            }

            unset($omy_temps, $temp);

            $sort_arr = [];

            $list_zone = [];

            foreach(["venue", "zone"] as $temp) {

                if(!isset($report_grouping[$temp])) continue;

                foreach ($report_grouping[$temp] as $key_zone => $zone_data) {


                    $time_arr = ["12AM","1AM","2AM","3AM","4AM","5AM","6AM","7AM","8AM","9AM","10AM","11AM","12PM","1PM","2PM","3PM","4PM","5PM","6PM","7PM","8PM","9PM","10PM","11PM"];
                    if(!in_array($key_zone, $list_zone)) {

                        $list_zone[] = $key_zone;
                        
                    }


                    foreach ($time_arr as $time) {

           
                        $sort_arr[$key_zone][] = $zone_data[$time] ?? ["time" => $time, "total" => 0];

                    }
          

                }
            }

            $reports["heatmap_zone"] = $sort_arr;
            $reports["list_zone"] = $list_zone;
            unset($sort_arr, $time_arr, $report_grouping, $key_zone, $zone_data, $temp);

            $range[] = ["from" => 0, "to" => 0, "color" => "#e2e6bd"];
            $range[] = ["from" => 1, "to" => 10, "color" => "#f1de81"];
            $range[] = ["from" => 11, "to" => 20, "color" => "#f6c971"];
            $range[] = ["from" => 21, "to" => 40, "color" => "#eeab65"];
            $range[] = ["from" => 41, "to" => 80, "color" => "#da8459"];
            $range[] = ["from" => 81, "to" => 120, "color" => "#b9534c"];
            $range[] = ["from" => 121, "to" => 99999, "color" => "#8e133b", "name" => "More 120"];

            $reports["range"] = $range;
            $reports["device-ap"] = $ap;
            $reports["venue"] = $venue;
            // return ["status" => "success", "data" => $response];


            return respondAjax("success", "", $reports);

        }
        return respondAjax("error", "Unrecognized request");


    }

    public function mapView($id)
    {
        

        if(!$venue = OmayaVenue::with('location')->where('venue_uid', $id)->first()) {

            return redirect(route('admin.manage.venue.index'))->withErrors(trans('alert.record-not-found'));

        }


        if(!$venue->image) return redirect(route('admin.manage.venue.index'))->withErrors("Please upload map first for this venue [ {$venue->name} ].");


        $devices = OmayaDeviceController::where('venue_uid', $id)->get();

        return view('v1.admin.analytics.venue_map.map', compact('venue', 'devices'));

    }

    public function liveHeatmap(){

        if(!$venue = OmayaVenue::first()) {

            return redirect(route('admin.manage.venue.index'))->withErrors(trans('alert.record-not-found'));

        }


        if(!$venue->image) return redirect(route('admin.manage.venue.index'))->withErrors("Please upload map first for this venue [ {$venue->name} ].");


        $devices = OmayaDeviceController::where('venue_uid', $venue->venue_uid)->get();

        return view('v1.admin.analytics.venue_map.live_map', compact('venue', 'devices'));
    }
}
