<?php

namespace App\Http\Controllers\v1\Admin\Analytics;

use App\Http\Controllers\Controller;
use App\Models\OmayaLocation;
use App\Models\OmayaVenue;
use Illuminate\Http\Request;

class AnalyticsCrossvisitController extends Controller
{
    public function index(){

        $min_date = getDateStart();

        $locations = OmayaLocation::with('venues', 'venues.zones')->get();

        return view('v1.admin.analytics.cross_visit.index', compact('locations', 'min_date'));
    }

    public function data(Request $request)
    {
            
        if ($request->ajax()) {


            foreach (['report_date', 'location', 'venue'] as $key => $value) {

                if(!$request->input($value)) 
                return respondAjax("warning", "Please select ". ucfirst(str_replace("_", " ", $value)) ." field.", []);
            }

            $report_date = explode(" to ", $request->input('report_date'));
            
            $date_start = $report_date[0];
            $date_end   = $report_date[1] ?? $report_date[0];


            $date_start = reportDateStart($date_start);
            $date_end   = reportDateEnd($date_end);

            $tables = getMonthlyTable($date_start, $date_end, session('timezone'), 'omaya_report_cross_visit');


           
            $omy_temps = [];
            foreach ($tables as $table) {

                if (!\Schema::hasTable($table)) continue;

                $temp_data = \DB::select("SELECT SQL_CACHE report_date, CONVERT_TZ(report_date, 'UTC', '". session('timezone') ."') AS xreport_date, 
                    DATE_FORMAT(CONVERT_TZ(report_date, 'UTC', '". session('timezone') ."'), '%d/%m/%y') as date, 
                    sum(total) as total_count,
                    location_uid,
                    venue_uid,
                    zone_uid,
                    location_name,
                    venue_name,
                    zone_name,
                    to_location_name,
                    to_venue_name,
                    to_zone_name
                    FROM {$table}
                    WHERE tenant_id = '". session('tenant_id') ."' 
                    AND 
                    (report_date BETWEEN '{$date_start}' AND '{$date_end}') 
                    AND
                    location_uid = '{$request->input('location')}' 
                    AND
                    venue_uid = '{$request->input('venue')}' 
                    GROUP BY date, to_location_uid, to_venue_uid ORDER BY date");


                // return "SELECT SQL_CACHE report_date, CONVERT_TZ(report_date, 'UTC', '". session('timezone') ."') AS xreport_date, 
                //     DATE_FORMAT(CONVERT_TZ(report_date, 'UTC', '". session('timezone') ."'), '%d/%m/%y') as date, 
                //     sum(total) as total_count,
                //     location_uid,
                //     venue_uid,
                //     zone_uid,
                //     location_name,
                //     venue_name,
                //     zone_name,
                //     to_location_name,
                //     to_venue_name,
                //     to_zone_name
                //     FROM {$table}
                //     WHERE tenant_id = '". session('tenant_id') ."' 
                //     AND 
                //     (report_date BETWEEN '{$date_start}' AND '{$date_end}') 
                //     AND
                //     location_uid = '{$request->input('location')}' 
                //     AND
                //     venue_uid = '{$request->input('venue')}' 
                //     GROUP BY date, to_location_uid, to_venue_uid ORDER BY date";    


                // $temp_data = \DB::select("SELECT SQL_CACHE report_date, CONVERT_TZ(report_date, 'UTC', '". session('timezone') ."') AS xreport_date, 
                //     DATE_FORMAT(CONVERT_TZ(report_date, 'UTC', '". session('timezone') ."'), '%d/%m/%y') as date, 
                //     sum(total) as total_count,
                //     location_uid,
                //     venue_uid,
                //     zone_uid,
                //     location_name,
                //     venue_name,
                //     zone_name,
                //     to_location_name,
                //     to_venue_name,
                //     to_zone_name
                //     to_location_uid,
                //     to_venue_uid,
                //     to_zone_uid
                //     FROM {$table}
                //     WHERE tenant_id = '". session('tenant_id') ."' 
                //     AND 
                //     (report_date BETWEEN '{$date_start}' AND '{$date_end}') 
                //     AND
                //     location_uid = '{$request->input('location')}' 
                //     AND
                //     venue_uid = '{$request->input('venue')}' 
                //     ". (!empty($request->input('zone')) ? " AND zone_uid = '{$request->input('zone')}' " : " ")
                //     ." AND (to_location_uid != '{$request->input('location')}'
                //     AND to_venue_uid = '{$request->input('venue')}')
                //      GROUP BY date, to_location_uid, to_venue_uid ORDER BY date");

                
                $omy_temps = array_merge($omy_temps, $temp_data);

        
            }


            if(empty($omy_temps)) {
                
                return respondAjax("info", "The are no record for this date range <br> [{$report_date[0]} - ". ($report_date[1] ?? $report_date[0]) ."]", $omy_temps);

            }



        //     {
        //   name: 'Level 5',
        //   data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
        // }, {
        //   name: 'Level 6',
        //   data: [76, 85, 101, 98, 87, 105, 91, 114, 94]
        // }, {
        //   name: 'Level 7',
        //   data: [35, 41, 36, 26, 45, 48, 52, 53, 0]
        // }

            unset($tables, $table, $temp_data);


            $report_grouping = [];

            foreach ($omy_temps as $temp) {

                $report_grouping["date"][] = $temp->date;

                $report_grouping["data"][$temp->venue_name]["name"] = $temp->venue_name;
                $report_grouping["data"][$temp->venue_name]["data"][] = $temp->total_count;


            }


            $reports = [];

            foreach ($report_grouping['data'] as $key => $value) {

                $reports["data"][] = $report_grouping['data'][$key];
                $reports["date"]   = $report_grouping["date"];
                // $reports["data"][] = 

            }



            return respondAjax("success", "", $reports);

        }
        return respondAjax("error", "Unrecognized request");



    }
}
