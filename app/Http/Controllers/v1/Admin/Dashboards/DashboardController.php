<?php

namespace App\Http\Controllers\v1\Admin\Dashboards;

use App\Http\Controllers\Controller;
use App\Models\OmayaDeviceTracker;
use App\Models\OmayaLocation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    
    public function index(){


        // $omy_db = new \mysqli("127.0.0.1", "omaya_main", "", "omaya", "3306");


        // $omy_tracker = $omy_db->query("SELECT SQL_CACHE * FROM omaya_device_trackers");

        // return $omy_tracker = $omy_tracker->fetch_all(MYSQLI_ASSOC)[0];


        // $a = OmayaDeviceTracker::get()->toArray();

        // return $a[0]['mac_address'];

        $omy_client['vendor']       = "Cyclistspot";
        $omy_client['product']      = "omaya";
        $omy_client['client_name']  = "cyclistspot";
        $omy_client['device_limit'] = 250;
        $omy_client['expire_on']    = strtotime(date('Y-m-d', strtotime(date("Y-m-d") . ' + 5 Years')));
        $omy_client['multi-tenant'] = false;
        $omy_client['type']         = "crowd";
        $omy_client['triangulation']= false;
        $omy_client['your_name']    = "Syncroweb-dev";
        $omy_client['generate_on']  = time();
        $omy_client['trial']        = true;
        $omy_license                = json_encode($omy_client);
        $omy_license                = openssl_encrypt($omy_license, "AES-256-CBC", "e1gOtk*9Ox_R", 0, "7vO*STBUdm_7tU4i");
        

        // cXpIQS9GS2I3dVlSdlc3dUVja2pCRFZwMWFVVE1YSU55SEYraTlnMEgvaHZvUnRyUU1OYUNCQ3lWVEhhYUhldWN6UjdkbXptemJvbExCdWpyektwWFhTenpwK0JqY1d4WU1Mb29CdW14YmEvTGI4NWVmTFFnNEJFa3N3Uzg4eHhUMUl6MGl2TThldjlxbmpUa0FsRmdCNk12TFJ0WGtEQkdLaFMweERVZkpRRjJtVkJpSVN6cS9LYlcrTWpsQXFYbU1ZN0M4aFlyK09BU1ovVkZnK1lVVVpCMGJjMkx0U1ZVVVJxVWNNaVc2aE9CMUhzUDlVTStpb2E2eVJLSm4ySjJHU2luYmhJeUkxcUNMRTdvQmRldXBsRlhrcElnQndXT1dxTko2ZXZhOGllTWxjQWp2ZC9tNXBvRTAwUklZUHc=

        // return sync_license_decode("RXA4ZlhIa0J4dlhobUdtT2NSSVdUY0RLUFkwOVE3NU9SSHZyQnBmWTg1a0NZa1R5UHdWbmJWVVhjL210YTdDOHN1RndtQXFGRWxhaWNMTS8xUCtHK2VrV0xoRjl0aWRpRDlwLzIvQWdxSHQweEc4V1V0UjdVWmdkK3c4Q0gyVW14WWd3QU1XcVVyZkNRYU93WTFPK2o3Z0NFZTU4MDZPZUgwU201VVRBQkE3YllGQnlMT1pDeTVJdzdKTlhRYlpGVTFWK0pLZTdpMWFnL0JzODJGL2ZOdWZuSDdUWklDZGR3UURhSlJXa0R6MUN1d3RybGJxUm9IQ3pGVVEvK1RKdmRsRllsSDJGNmFNc25PTklJTXlBTW1HQ2N3bkZGdy84OHZsdnZrOUZRaG1lWFJBQUdIVDhzM0VKek9RN2I2bHg=");
        // return base64_encode($omy_license);

// \   Debugbar::info("jhb");



        if(empty(session("dashboard_location")) || empty(session("dashboard_venue")))  {

            $user = \Auth::user();
            \Session::put('dashboard_location'   , $user->location_uid);
            \Session::put('dashboard_venue'   , $user->venue_uid);

            unset($user);

        }


        $locations = OmayaLocation::with('venues')->get();

        return view('v1.admin.dashboard.index', compact('locations'));
        
    }

    public function data(Request $request){


        \Session::put('dashboard_location'   , $request->input('location'));
        \Session::put('dashboard_venue'   , $request->input('venue'));


        $date_end    = date("Y-m-d H:00:00", strtotime("-1 Hour"));
        $date_start  = date("Y-m-d H:00:00", strtotime("{$date_end} -23 Hour"));

        \Auth::user()->update([
            'location_uid' => $request->input('location'),
            'venue_uid' => $request->input('venue'),
        ]);


        $tables = getMonthlyTable($date_start, $date_end, session('timezone'), 'omaya_report_general');
        $tables_2 = getMonthlyTable($date_start, $date_end, session('timezone'), 'omaya_report_heatmap');


        $omy_temps = [];
        foreach ($tables as $table) {


            if (!\Schema::hasTable($table)) continue;

            $temp_data = \DB::select("SELECT SQL_CACHE report_date, CONVERT_TZ(report_date, 'UTC', '". session('timezone') ."') AS xreport_date, 
                DATE_FORMAT(CONVERT_TZ(report_date, 'UTC', '". session('timezone') ."'), '%d  %b %y / %a') as date, 
                TIME_FORMAT(CONVERT_TZ(report_date, 'UTC', '". session('timezone') ."'), '%l%p') as time, 
                sum(total) as total_count,
                sum(passby) as passby_count,
                sum(visit) as visit_count,
                sum(engaged) as engaged_count,
                sum(new_device) as new_device_count,
                sum(return_device) as return_device_count
                FROM {$table}
                WHERE tenant_id = '". session('tenant_id') ."' 
                AND 
                (report_date BETWEEN '{$date_start}' AND '{$date_end}') 
                AND
                location_uid = '". session('dashboard_location') ."' 
                AND
                venue_uid = '". session('dashboard_venue') ."'
                GROUP BY report_date ORDER BY report_date");
            
            
            $omy_temps = array_merge($omy_temps, $temp_data);

        }

        if(empty($omy_temps)) {
                    
            return respondAjax("info", "The are no more details for this date range <br> [{$report_date[0]} - ". ($report_date[1] ?? $report_date[0]) ."]", $omy_temps);

        }

        unset($tables, $table, $temp_data);



        $omy_temps_2 = [];
        foreach ($tables_2 as $table_2) {

            if (!\Schema::hasTable($table_2)) continue;

            $temp_data_2 = \DB::select("SELECT SQL_CACHE report_date, CONVERT_TZ(report_date, 'UTC', '". session('timezone') ."') AS xreport_date, 
                DATE_FORMAT(CONVERT_TZ(report_date, 'UTC', '". session('timezone') ."'), '%d  %b %y / %a') as date, 
                TIME_FORMAT(CONVERT_TZ(report_date, 'UTC', '". session('timezone') ."'), '%l%p') as time, 
                sum(total) as total_count
                FROM {$table_2}
                WHERE tenant_id = '". session('tenant_id') ."' 
                AND 
                (report_date BETWEEN '{$date_start}' AND '{$date_end}') 
                AND
                location_uid = '". session('dashboard_location') ."' 
                AND
                venue_uid = '". session('dashboard_venue') ."' 
                GROUP BY report_date ORDER BY report_date");
            
            
            $omy_temps_2 = array_merge($omy_temps_2, $temp_data_2);

        }

        if(empty($omy_temps_2)) {
            
            return respondAjax("info", "The are no record for this date range <br> [{$report_date[0]} - ". ($report_date[1] ?? $report_date[0]) ."]", $omy_temps_2);

        }


        unset($tables_2, $table_2, $temp_data_2);

        $report_grouping = [];
        foreach ($omy_temps as $temp) {


            $report_grouping["series"]["passby"][0]["name"] = "Passby";
            $report_grouping["series"]["passby"][0]["data"][] = $temp->passby_count;
            $report_grouping["categories"]["passby"][] = $temp->time;


            $report_grouping["series"]["visit"][0]["name"] = "Visit";
            $report_grouping["series"]["visit"][0]["data"][] = $temp->visit_count;
            $report_grouping["categories"]["visit"][] = $temp->time;

            $report_grouping["series"]["engaged"][0]["name"] = "Engaged";
            $report_grouping["series"]["engaged"][0]["data"][] = $temp->engaged_count;
            $report_grouping["categories"]["engaged"][] = $temp->time;


            $report_grouping["series"]["new_device"][0]["name"] = "New Device";
            $report_grouping["series"]["new_device"][0]["data"][] = $temp->new_device_count;
            $report_grouping["categories"]["new_device"][] = $temp->time;


            $report_grouping["series"]["return_device"][0]["name"] = "Return Device";
            $report_grouping["series"]["return_device"][0]["data"][] = $temp->return_device_count;
            $report_grouping["categories"]["return_device"][] = $temp->time;



        }

        unset($omy_temps, $temp);



        foreach ($omy_temps_2 as $temp_2) {

            $report_grouping["series"]["heatmap"][0]["name"] = "Heatmap";
            $report_grouping["series"]["heatmap"][0]["data"][] = $temp_2->total_count;
            $report_grouping["categories"]["heatmap"][] = $temp_2->time;

        }



        $report_grouping["heatmap"] = $omy_temps_2;

        unset($omy_temps_2, $temp_2);

        return respondAjax("success", "", $report_grouping);



        return $omy_temps;






        return $request;

    }



}
