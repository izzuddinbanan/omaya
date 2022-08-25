<?php

namespace App\Http\Controllers\v1\Admin\Analytics;

use App\Http\Controllers\Controller;
use App\Models\OmayaLocation;
use App\Models\OmayaReportDwell;
use Illuminate\Http\Request;

class AnalyticsDwelltimeController extends Controller
{
    public function index(){


        $min_date = getDateStart();

        $locations = OmayaLocation::with('venues', 'venues.zones')->get();

        return view('v1.admin.analytics.dwell_time.index', compact('locations', 'min_date'));

    }

    public function data(Request $request)
    {

        if ($request->ajax()) {


            $report_date = explode(" to ", $request->input('report_date'));
            
            $date_start = $report_date[0];
            $date_end   = $report_date[1] ?? $report_date[0];



            $type = $request->input('type') == "all" ? "" : "_" . $request->input('type');


            $date_start = reportDateStart($date_start);
            $date_end   = reportDateEnd($date_end);

            $tables = getMonthlyTable($date_start, $date_end, session('timezone'), 'omaya_report_dwell');


            $omy_temps = [];
            foreach ($tables as $table) {

                $temp_data = \DB::select("SELECT DATE(CONVERT_TZ(report_date, 'UTC', '". session('timezone') ."')) AS xreport_date, 
                DATE_FORMAT(CONVERT_TZ(report_date, 'UTC', '". session('timezone') ."'), '%d/%m/%y') as date, 
                SUM(total_dwell_engaged{$type}) as total_dwell_engaged,
                SUM(total_dwell_engaged{$type}) as total_dwell_engaged,
                SUM(dwell_15{$type}) as dwell_15,
                SUM(dwell_30{$type}) as dwell_30,
                SUM(dwell_60{$type}) as dwell_60,
                SUM(dwell_120{$type}) as dwell_120,
                SUM(dwell_240{$type}) as dwell_240,
                SUM(dwell_480{$type}) as dwell_480,
                SUM(dwell_more{$type}) as dwell_more,
                SUM(total_dwell{$type}) as total_dwell,
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
                ". (!empty($request->input('zone')) ? " AND zone_uid = '{$request->input('zone')}' " : " ") ."
                GROUP BY date");
            
            
                $omy_temps = array_merge($omy_temps, $temp_data);

            }

            

            if(empty($omy_temps)) {
                
                return respondAjax("info", "The are no record for this date range <br> [{$report_date[0]} - ". ($report_date[1] ?? $report_date[0]) ."]", $omy_temps);

            }




            $reports = [];

            $report_date = [];
            foreach ($omy_temps as $temp) {

                $reports["date"][] = $temp->date;
                // $report_date[] = ["name"=> "Total Device", "data" => ];

                $test["dwell_15"][] = $temp->dwell_15;
                $test["dwell_30"][] = $temp->dwell_30;
                $test["dwell_60"][] = $temp->dwell_60;
                $test["dwell_120"][] = $temp->dwell_120;
                $test["dwell_240"][] = $temp->dwell_240;
                $test["dwell_480"][] = $temp->dwell_480;
                $test["dwell_more"][] = $temp->dwell_more;



            }

            $reports['chart'][] = ["name" => "< 15 Min" , "data" => $test["dwell_15"]];
            $reports['chart'][] = ["name" => "15-30 Min" , "data" => $test["dwell_30"]];
            $reports['chart'][] = ["name" => "30 Min - 1 Hour" , "data" => $test["dwell_60"]];
            $reports['chart'][] = ["name" => "1-2 Hour" , "data" => $test["dwell_120"]];
            $reports['chart'][] = ["name" => "2-4 Hour" , "data" => $test["dwell_240"]];
            $reports['chart'][] = ["name" => "4-8 Hour" , "data" => $test["dwell_480"]];
            $reports['chart'][] = ["name" => "> 8 Hour" , "data" => $test["dwell_more"]];


            
            return respondAjax("success", "", $reports);

        }
        
        return respondAjax("error", "Unrecognized request");


    }






    
}
