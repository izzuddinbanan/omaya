<?php

namespace App\Http\Controllers\v1\Admin\Analytics;

use App\Http\Controllers\Controller;
use App\Models\OmayaLocation;
use App\Models\OmayaZone;
use Illuminate\Http\Request;

class AnalyticsVisitController extends Controller
{
    public function index(){


        $min_date = getDateStart();

        $locations = OmayaLocation::with('venues', 'venues.zones')->get();

        return view('v1.admin.analytics.visit.index', compact('locations', 'min_date'));
    }


    public function data(Request $request)
    {
            
        if ($request->ajax()) {


            foreach (['report_date', 'location', 'venue', 'scanner_type'] as $key => $value) {

                if(!$request->input($value)) 
                return respondAjax("warning", "Please select ". ucfirst(str_replace("_", " ", $value)) ." field.", []);
            }

            $report_date = explode(" to ", $request->input('report_date'));
            
            $date_start = $report_date[0];
            $date_end   = $report_date[1] ?? $report_date[0];



            $scanner_type = $request->input('scanner_type') == "all" ? "" : "_" . ($request->input('scanner_type') == "wifi" ? "wifi" : "ble");


            $date_start = reportDateStart($date_start);
            $date_end   = reportDateEnd($date_end);

            $tables = getMonthlyTable($date_start, $date_end, session('timezone'), 'omaya_report_general');


            $count_unique = $request->input('unique_count') == "yes" ? "unique_" : "";

            
            if($request->input('report_type') != "hourly") {

                $omy_temps = [];
                foreach ($tables as $table) {

                    if (!\Schema::hasTable($table)) continue;

                    $temp_data = \DB::select("SELECT SQL_CACHE report_date, CONVERT_TZ(report_date, 'UTC', '". session('timezone') ."') AS xreport_date, 
                        DATE_FORMAT(CONVERT_TZ(report_date, 'UTC', '". session('timezone') ."'), '%d/%m/%y') as date, 
                        sum({$count_unique}total{$scanner_type}) as total_device,
                        sum({$count_unique}engaged{$scanner_type}) as engaged,
                        sum({$count_unique}passby{$scanner_type}) as passby,
                        sum({$count_unique}visit{$scanner_type}) as visit,
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
                        ". (!empty($request->input('zone')) ? " AND zone_uid = '{$request->input('zone')}' " : " ")
                        . " GROUP BY date ORDER BY date");
                    
                    $omy_temps = array_merge($omy_temps, $temp_data);

                }

                if(empty($omy_temps)) {
                    
                    return respondAjax("info", "The are no record for this date range <br> [{$report_date[0]} - ". ($report_date[1] ?? $report_date[0]) ."]", $omy_temps);

                }




                unset($tables, $table, $temp_data);

                // $data['date'] = getEachDateBetweenDate($report_date[0], $report_date[1] ?? $report_date[0], "d/m/y");


                $test = [];
                $reports = [];

                $report_date = [];
                foreach ($omy_temps as $temp) {

                    $reports["date"][] = $temp->date;
                    // $report_date[] = ["name"=> "Total Device", "data" => ];

                    $test["total_device"][] = $temp->total_device;
                    $test["engaged"][] = $temp->engaged;
                    $test["passby"][] = $temp->passby;
                    $test["visitor"][] = $temp->visit;



                }

                $reports['chart'][] = ["name" => "Total Device" , "data" => $test["total_device"]];
                $reports['chart'][] = ["name" => "Engaged" , "data" => $test["engaged"]];
                $reports['chart'][] = ["name" => "Passby" , "data" => $test["passby"]];
                $reports['chart'][] = ["name" => "Visitor" , "data" => $test["visitor"]];


                return respondAjax("success", "", $reports);
            }else {


                $omy_temps = [];
                foreach ($tables as $table) {

                    if (!\Schema::hasTable($table)) continue;

                    $temp_data = \DB::select("SELECT SQL_CACHE report_date, CONVERT_TZ(report_date, 'UTC', '". session('timezone') ."') AS xreport_date, 
                        DATE_FORMAT(CONVERT_TZ(report_date, 'UTC', '". session('timezone') ."'), '%d  %b %y / %a') as date, 
                        TIME_FORMAT(CONVERT_TZ(report_date, 'UTC', '". session('timezone') ."'), '%l%p') as time, 
                        sum({$count_unique}visit{$scanner_type}) as visit_count,
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
                        ". (!empty($request->input('zone')) ? " AND zone_uid = '{$request->input('zone')}' " : " ")
                        . " GROUP BY report_date ORDER BY report_date");
                    
                    
                    $omy_temps = array_merge($omy_temps, $temp_data);

                }

                if(empty($omy_temps)) {
                        
                    return respondAjax("info", "The are no more details for this date range <br> [{$report_date[0]} - ". ($report_date[1] ?? $report_date[0]) ."]", $omy_temps);

                }

                unset($tables, $table, $temp_data);

                $report_grouping = [];
                foreach ($omy_temps as $temp) {

                    if(!isset($report_grouping[$temp->date])) {

                        $report_grouping["visit"][$temp->date]["name"] = $temp->date;

                    }

                    $report_grouping["visit"][$temp->date]["data"][$temp->time] = ["x" => $temp->time, "y" => $temp->visit_count];


                }

                $temp_row_count = count($report_grouping["visit"] ?? []);



                // Sort data to match the JS data format 
                $charts = [];
                foreach (["visit"] as $value_report_type) {

                    foreach ($report_grouping[$value_report_type] as $key_day => $day) {

                        $time_arr = ["12AM","1AM","2AM","3AM","4AM","5AM","6AM","7AM","8AM","9AM","10AM","11AM","12PM","1PM","2PM","3PM","4PM","5PM","6PM","7PM","8PM","9PM","10PM","11PM"];
                            
                        $sort_arr = [];
                        foreach ($time_arr as $time) {

                            $sort_arr[] = $day["data"][$time] ?? ["x" => $time, "y" => 0];


                        }
                        $day["data"] = $sort_arr;
                        $charts[$value_report_type][] = $day;

                    }
                }

                $reports["chart"] = $charts;

                $reports["row_count"] =  $temp_row_count;


                $range[] = ["from" => 0, "to" => 0, "color" => "#e2e6bd"];
                $range[] = ["from" => 1, "to" => 10, "color" => "#f1de81"];
                $range[] = ["from" => 11, "to" => 20, "color" => "#f6c971"];
                $range[] = ["from" => 21, "to" => 40, "color" => "#eeab65"];
                $range[] = ["from" => 41, "to" => 80, "color" => "#da8459"];
                $range[] = ["from" => 81, "to" => 120, "color" => "#b9534c"];
                $range[] = ["from" => 121, "to" => 99999, "color" => "#8e133b", "name" => "More 120"];

                $reports["range"] = $range;
                // return ["status" => "success", "data" => $response];


                return respondAjax("success", "", $reports);


            }

        }
        return respondAjax("error", "Unrecognized request");



    }
}
