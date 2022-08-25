<?php

namespace App\Http\Controllers\v1\Admin\Analytics;

use App\Http\Controllers\Controller;
use App\Models\OmayaLocation;
use Illuminate\Http\Request;

class AnalyticsBenchmarkController extends Controller
{
    public function index(){

        $min_date = getDateStart();
        return view('v1.admin.analytics.benchmark.index', compact('min_date'));

    }


    public function data(Request $request)
    {
            
        if ($request->ajax()) {


            foreach (['report_date', 'scanner_type'] as $key => $value) {

                if(!$request->input($value)) 
                return respondAjax("warning", "Please select ". ucfirst(str_replace("_", " ", $value)) ." field.", []);
            }

            $report_date = explode(" to ", $request->input('report_date'));
            
            $date_start = $report_date[0];
            $date_end   = $report_date[1] ?? $report_date[0];


            $diff_start = new \DateTime($date_start, new \DateTimeZone(session('timezone')));
            $diff_end   = new \DateTime($date_end, new \DateTimeZone(session('timezone')));
            $omy_interval = $diff_start->diff($diff_end);
            $omy_interval = $omy_interval->days == 0 ? 1 : $omy_interval->days;


            $scanner_type = $request->input('scanner_type') == "all" ? "" : "_" . ($request->input('scanner_type') == "wifi" ? "wifi" : "ble");


            $date_start = reportDateStart($date_start);
            $date_end   = reportDateEnd($date_end);



            ######################
            // START PROCESS DATA
            #####################

            $omy_collect = $this->pullData($date_start, $date_end, $omy_interval, $scanner_type);
            
            if($omy_collect['status'] == false) {

                return respondAjax("info", $omy_collect['message'], $omy_collect['data']);

            }


            ###########################
            ## COMPARE DATA
            #######################


            $diff_end = date('Y-m-d H:i:s', strtotime("-1 second", strtotime($date_start)));
            $diff_start = date('Y-m-d H:i:s', strtotime("- {$omy_interval} day", strtotime($date_start)));


            $omy_reports = $this->pullData($diff_start, $diff_end, $omy_interval, $scanner_type, false, $omy_collect['data']);

            if($omy_reports['status'] == false) {

                return respondAjax("info", $omy_reports['message'], []);

            }


            $omy_data = [];
            foreach ($omy_reports['data'] as $key => $value) {

                $omy_data[] = $omy_reports['data'][$key];

            }

            return respondAjax("success", NULL, $omy_data);







        }
        return respondAjax("error", "Unrecognized request");



    }



    public function pullData($date_start, $date_end, $omy_interval, $scanner_type, $is_new_report = true, $omy_new_reports = [])
    {

        $tables = getMonthlyTable($date_start, $date_end, session('timezone'), 'omaya_report_general');


        // CHECKING TABLE
        foreach ($tables as $key => $table) {



            $omy_existed = \DB::select(\DB::raw("SELECT COUNT(*) AS ocount FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = '{$table}' AND TABLE_SCHEMA = 'omaya'"))[0];

            if ($omy_existed->ocount == 0) {

                unset($tables[$key]);
            }


        }

        $omy_temps = [];
        foreach ($tables as $table) {


            $temp_data = \DB::select("SELECT location_uid, venue_uid, zone_uid, location_name, venue_name, zone_name, 
                SUM(total{$scanner_type}) as total,
                SUM(unique_total{$scanner_type}) as unique_total,
                
                SUM(passby{$scanner_type}) as passby,
                SUM(unique_passby{$scanner_type}) as unique_passby,
                
                SUM(visit{$scanner_type}) as visit,
                SUM(unique_visit{$scanner_type}) as unique_visit,


                SUM(engaged{$scanner_type}) as engaged,
                SUM(unique_engaged{$scanner_type}) as unique_engaged,

                SUM(new_device{$scanner_type}) as new_device,
                SUM(return_device{$scanner_type}) as return_device

                FROM {$table} 
                WHERE tenant_id = '". session('tenant_id') ."' 
                AND 
                (report_date BETWEEN '{$date_start}' AND '{$date_end}') 
                GROUP BY location_uid, venue_uid, zone_uid 
                ");

            $omy_temps = array_merge($omy_temps, $temp_data);

        }



        if(empty($omy_temps)) {
                
            if($is_new_report) {

            
                return ["status" => false, "message" => "The are no record for this date range. Please select other date.", "data" => NULL];
            
           
            }else {


                $diff_data = ["perc_total"          => 0, 
                            "perc_unique_total"     => 0,
                            "perc_passby"           => 0,
                            "perc_unique_passby"    => 0,
                            "perc_visit"            => 0,
                            "perc_unique_visit"     => 0,
                            "perc_engaged"          => 0,
                            "perc_unique_engaged"   => 0,
                            "perc_new_device"       => 0,
                            "perc_return_device"    => 0,
                            ];

                foreach (['total', 'unique_total', 'engaged', 'unique_engaged', 'visit', 'unique_visit', 'passby', 'unique_passby', 'new_device', 'return_device'] as $col) {

                    $diff_data["old_{$col}"] = 0;
                    

                }


                foreach ($omy_new_reports as $key => $value) {

                    $omy_new_reports[$key] = array_merge($omy_new_reports[$key], $diff_data);
                }

                $omy_reports = $omy_new_reports;

                unset($diff_data, $key, $value, $omy_new_reports);

            }


        }else {




            // Merge data same location venue zone that have in different table
            if(count($tables) > 1) {

                $cal = [];
                foreach ($omy_temps as  $value) {
                    
                    if(empty($value->zone_uid)) $value->zone_uid = "nozone";

                    
                    if(isset($cal[$value->location_uid][$value->venue_uid][$value->zone_uid])) {


                        foreach (['total', 'unique_total', 'engaged', 'unique_engaged', 'visit', 'unique_visit', 'passby', 'unique_passby', 'new_device', 'return_device'] as $col) {

                            
                            $cal[$value->location_uid][$value->venue_uid][$value->zone_uid]->$col = $cal[$value->location_uid][$value->venue_uid][$value->zone_uid]->$col + $value->$col;


                        }

                        unset($col);

                    }else {

                    
                        $cal[$value->location_uid][$value->venue_uid][$value->zone_uid] = $value;

                    }

                }

                unset($value);


                $omy_temps = [];
                foreach ($cal as $location_uid => $venues) {
                    foreach ($venues as $venue_uid => $zones) {

                        foreach ($zones as $zone_uid => $temp_data) {

                            $temp_data->zone_uid = $temp_data->zone_uid == "nozone" ? NULL : $temp_data->zone_uid;
                            $omy_temps[] = $temp_data;
                        }
                    }
                }

                unset($cal, $location_uid, $venues, $venue_uid, $zones, $zone_uid, $temp_data, $tables, $table);

            }


            foreach ($omy_temps as $value) {

                $omy_reports["{$value->location_uid}_{$value->venue_uid}_{$value->zone_uid}"] = ['location_uid'    => $value->location_uid,
                                'venue_uid'         => $value->venue_uid,
                                'zone_uid'          => $value->zone_uid,
                                'location_name'     => $value->location_name,
                                'venue_name'        => $value->venue_name,
                                'zone_name'         => $value->zone_name,
                                'row_uid'           => rand(99,999) . rand(9,99) . md5(rand(1,999)),
                                'total'             => round($value->total / $omy_interval, 0, PHP_ROUND_HALF_UP),
                                'unique_total'      => round($value->unique_total / $omy_interval, 0, PHP_ROUND_HALF_UP),

                                'passby'            => round($value->passby    / $omy_interval, 0, PHP_ROUND_HALF_UP),
                                'unique_passby'     => round($value->unique_passby / $omy_interval, 0, PHP_ROUND_HALF_UP),
                                'engaged'           => round($value->engaged / $omy_interval, 0, PHP_ROUND_HALF_UP),
                                'unique_engaged'    => round($value->unique_engaged / $omy_interval, 0, PHP_ROUND_HALF_UP),
                                'visit'             => round($value->visit / $omy_interval, 0, PHP_ROUND_HALF_UP),
                                'unique_visit'      => round($value->unique_visit / $omy_interval, 0, PHP_ROUND_HALF_UP),

                                'new_device'        => round($value->new_device / $omy_interval, 0, PHP_ROUND_HALF_UP),
                                'return_device'     => round($value->return_device / $omy_interval, 0, PHP_ROUND_HALF_UP),

                                ];

            }



            if($is_new_report == false) {



                foreach ($omy_new_reports as $key => $value) {

                    if(isset($omy_reports["{$value['location_uid']}_{$value['venue_uid']}_{$value['zone_uid']}"])) {


                        foreach (['total', 'unique_total', 'engaged', 'unique_engaged', 'visit', 'unique_visit', 'passby', 'unique_passby', 'new_device', 'return_device'] as $col) {




                            $total_old = $omy_reports["{$value['location_uid']}_{$value['venue_uid']}_{$value['zone_uid']}"][$col];
                            $diff_data["old_{$col}"] = $total_old;
                            

                            // LOGIC CALCULATE PERCENTAGE
                            if($total_old <= 0) {
                                $diff_data["perc_{$col}"] = 0;
                            }
                            else {

                                $diff_data["perc_{$col}"] = (($value[$col] - $total_old) / $total_old) * 100;
                                $diff_data["perc_{$col}"] = round($diff_data["perc_{$col}"], 2);
                                
                                // $diff_data["perc_{$col}"] = $diff_data["perc_{$col}"] < 0 ? abs($diff_data["perc_{$col}"]) : "-{$diff_data["perc_{$col}"]}";
                            }



                        }


                    }else {



                        $diff_data = ["perc_total"  => 0, 
                            "perc_unique_total"     => 0,
                            "perc_passby"           => 0,
                            "perc_unique_passby"    => 0,
                            "perc_visit"            => 0,
                            "perc_unique_visit"     => 0,
                            "perc_engaged"          => 0,
                            "perc_unique_engaged"   => 0,
                            "perc_new_device"       => 0,
                            "perc_return_device"    => 0,


                            ];


                        foreach (['total', 'unique_total', 'engaged', 'unique_engaged', 'visit', 'unique_visit', 'passby', 'unique_passby', 'new_device', 'return_device'] as $col) {

                            $diff_data["old_{$col}"] = 0;
                            

                        }


                    }


                    $omy_new_reports[$key] = array_merge($omy_new_reports[$key], $diff_data);

                }

                $omy_reports = $omy_new_reports;

                unset($diff_data, $omy_diff_reports, $omy_temps);


            }


        }

        return ["status" => true, "message" => "", "data" => $omy_reports];

        unset($omy_temps, $value);


    }



    public function heatmap(Request $request)
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
            
            $report_type  = $request->input('report_type') == "" ? "" : "unique_";


            $date_start = reportDateStart($date_start);
            $date_end   = reportDateEnd($date_end);

            $tables = getMonthlyTable($date_start, $date_end, session('timezone'), 'omaya_report_general');


            $omy_temps = [];
            foreach ($tables as $table) {

                if (!\Schema::hasTable($table)) continue;

                $temp_data = \DB::select("SELECT SQL_CACHE report_date, CONVERT_TZ(report_date, 'UTC', '". session('timezone') ."') AS xreport_date, 
                    DATE_FORMAT(CONVERT_TZ(report_date, 'UTC', '". session('timezone') ."'), '%d  %b %y / %a') as date, 
                    TIME_FORMAT(CONVERT_TZ(report_date, 'UTC', '". session('timezone') ."'), '%l%p') as time, 
                    sum({$report_type}total{$scanner_type}) as total_count,
                    sum({$report_type}passby{$scanner_type}) as passby_count,
                    sum({$report_type}visit{$scanner_type}) as visit_count,
                    sum({$report_type}engaged{$scanner_type}) as engaged_count,
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

                    $report_grouping["total"][$temp->date]["name"] = $temp->date;
                    $report_grouping["passby"][$temp->date]["name"] = $temp->date;
                    $report_grouping["visit"][$temp->date]["name"] = $temp->date;
                    $report_grouping["engaged"][$temp->date]["name"] = $temp->date;

                }

                $report_grouping["total"][$temp->date]["data"][$temp->time] = ["x" => $temp->time, "y" => $temp->total_count];
                $report_grouping["passby"][$temp->date]["data"][$temp->time] = ["x" => $temp->time, "y" => $temp->passby_count];
                $report_grouping["visit"][$temp->date]["data"][$temp->time] = ["x" => $temp->time, "y" => $temp->visit_count];
                $report_grouping["engaged"][$temp->date]["data"][$temp->time] = ["x" => $temp->time, "y" => $temp->engaged_count];


            }

            $temp_row_count = count($report_grouping["total"] ?? []);



            // Sort data to match the JS data format 
            $charts = [];
            foreach (["total", "passby", "visit", "engaged"] as $value_report_type) {

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


            return $tables;
        }

        return respondAjax("error", "Unrecognized request");


    }

}

