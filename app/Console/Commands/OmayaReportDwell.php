<?php

namespace App\Console\Commands;

use App\Models\OmayaCloud;
use App\Models\OmayaDeviceController;
use App\Models\OmayaLocation;
use App\Models\OmayaReportDwell as ReportDwell;
use App\Models\OmayaVenue;
use App\Models\OmayaZone;
use Illuminate\Console\Command;

class OmayaReportDwell extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:dwell';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $omy_time['start'] = date('Y-m-d H:i:s');
        $omy_cache = redisCache();


        // Get all tenant
        $tenants = OmayaCloud::get()->each(function($tenant) use($omy_cache) {


            $omy_processing_id = time();

            \DB::update("UPDATE omaya_raw_massages SET dwell_report_status = '{$omy_processing_id}' WHERE tenant_id = '{$tenant->tenant_id}' AND first_seen_at != last_seen_at AND dwell_report_status = 'pending'");

            ###########################


            $controllers = \DB::select("SELECT mac_address_ap FROM omaya_raw_massages WHERE tenant_id = '{$tenant->tenant_id}' AND dwell_report_status = '{$omy_processing_id}' GROUP BY mac_address_ap");



            $omy_dwells = [];
            foreach ($controllers as $controller){


                // Get device_controller AP Data
                $omy_ap = $omy_cache->hGetAll("DEVICE:AP:DATA:{$controller->mac_address_ap}");
                if (empty($omy_ap)) {

                    $omy_ap = OmayaDeviceController::withoutGlobalScopes()->where('mac_address', $controller->mac_address_ap)->get()->toArray();

                    if (empty($omy_ap)) continue;
                    else $omy_cache->hMSet("DEVICE:AP:DATA:{$controller->mac_address_ap}", $omy_ap);

                }
                if(empty($omy_ap["zone_uid"])) $omy_ap["zone_uid"] = "nozone"; 




                $raws = \DB::select("SELECT * FROM omaya_raw_massages WHERE tenant_id = '{$tenant->tenant_id}' AND dwell_report_status = '{$omy_processing_id}' AND mac_address_ap = '{$controller->mac_address_ap}'");

                

                \DB::update("UPDATE omaya_raw_massages SET dwell_report_status = 'completed'  WHERE tenant_id = '{$tenant->tenant_id}' AND dwell_report_status = '{$omy_processing_id}' AND mac_address_ap = '{$controller->mac_address_ap}'");



                foreach ($raws as $raw) {



                    $omy_dwells[$raw->dwell_report_table][$raw->report_time][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']][$raw->dwell_group_now]['add'] = ($omy_dwells[$raw->dwell_report_table][$raw->report_time][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']][$raw->dwell_group_now]['add'] ?? 0) + 1;


                    $omy_dwells[$raw->dwell_report_table][$raw->report_time][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']]["{$raw->dwell_group_now}_{$raw->rssi_type}"]['add'] = ($omy_dwells[$raw->dwell_report_table][$raw->report_time][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']]["{$raw->dwell_group_now}_{$raw->rssi_type}"]['add'] ?? 0) + 1;


                    if(!empty($raw->dwell_group_last)) {


                        $omy_dwells[$raw->dwell_report_table][$raw->report_time][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']][$raw->dwell_group_last]['minus'] = ($omy_dwells[$raw->dwell_report_table][$raw->report_time][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']][$raw->dwell_group_last]['minus'] ?? 0) + 1;

                        $omy_dwells[$raw->dwell_report_table][$raw->report_time][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']]["{$raw->dwell_group_last}_{$raw->rssi_type}"]['minus'] = ($omy_dwells[$raw->dwell_report_table][$raw->report_time][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']]["{$raw->dwell_group_last}_{$raw->rssi_type}"]['minus'] ?? 0) + 1;


                    }



                    // Get All Total Dwell

                    $omy_dwells[$raw->dwell_report_table][$raw->report_time][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']]['total_dwell']['add'] = ($omy_dwells[$raw->dwell_report_table][$raw->report_time][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']]['total_dwell']['add'] ?? 0) + $raw->dwell_now;

                    $omy_dwells[$raw->dwell_report_table][$raw->report_time][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']]["total_dwell_{$raw->rssi_type}"]['add'] = ($omy_dwells[$raw->dwell_report_table][$raw->report_time][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']]["total_dwell_{$raw->rssi_type}"]['add'] ?? 0) + $raw->dwell_now;



                    if(!empty($raw->dwell_last)) {


                        $omy_dwells[$raw->dwell_report_table][$raw->report_time][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']]['total_dwell']['minus'] = ($omy_dwells[$raw->dwell_report_table][$raw->report_time][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']]['total_dwell']['minus'] ?? 0) + $raw->dwell_last;

                        $omy_dwells[$raw->dwell_report_table][$raw->report_time][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']]["total_dwell_{$raw->rssi_type}"]['minus'] = ($omy_dwells[$raw->dwell_report_table][$raw->report_time][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']]["total_dwell_{$raw->rssi_type}"]['minus'] ?? 0) + $raw->dwell_last;


                    }



                    // Total Dwell Engaged
                    // Rule engage rssi must less than rssi_min and must more than dwell_time for engaged
                    if( abs($raw->rssi) < ($raw->rssi_type == 'wifi' ?  abs($omy_ap['rssi_max']) : abs($omy_ap['rssi_max_ble'])) && ($raw->dwell_now) > $omy_ap['dwell_time'] ) {

                        
                        $omy_dwells[$raw->dwell_report_table][$raw->report_time][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']]['total_dwell_engaged']['add'] = ($omy_dwells[$raw->dwell_report_table][$raw->report_time][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']]['total_dwell_engaged']['add'] ?? 0) + $raw->dwell_now;


                        $omy_dwells[$raw->dwell_report_table][$raw->report_time][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']]["total_dwell_engaged_{$raw->rssi_type}"]['add'] = ($omy_dwells[$raw->dwell_report_table][$raw->report_time][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']]["total_dwell_engaged_{$raw->rssi_type}"]['add'] ?? 0) + $raw->dwell_now;


                        if(!empty($raw->dwell_last)) {


                            if($raw->is_engaged == true) {


                                $omy_dwells[$raw->dwell_report_table][$raw->report_time][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']]['total_dwell_engaged']['minus'] = ($omy_dwells[$raw->dwell_report_table][$raw->report_time][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']]['total_dwell_engaged']['minus'] ?? 0) + $raw->dwell_last;


                                $omy_dwells[$raw->dwell_report_table][$raw->report_time][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']]["total_dwell_engaged_{$raw->rssi_type}"]['minus'] = ($omy_dwells[$raw->dwell_report_table][$raw->report_time][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']]["total_dwell_engaged_{$raw->rssi_type}"]['minus'] ?? 0) + $raw->dwell_last;


                            }else {

                                \DB::update("UPDATE omaya_raw_massages SET is_engaged = '1' WHERE id = '{$raw->id}'");

                            }

                        }

                    }






                }


            }

            unset($raw, $raws, $controllers, $controller);


            foreach($omy_dwells as $report_table => $report_table_data){



                $omy_existed = $omy_cache->get("TABLE_CHECKED:{$report_table}");

                if (empty($omy_existed)) {


                    $omy_existed = \DB::select(\DB::raw("SELECT COUNT(*) AS ocount FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = '{$report_table}' AND TABLE_SCHEMA = 'omaya'"))[0];


                    if ($omy_existed->ocount == 0) \DB::select(\DB::raw("CREATE TABLE {$report_table} LIKE omaya_report_dwell_templates"));

                    $omy_cache->set("TABLE_CHECKED:{$report_table}", "checked");
                }

                unset($omy_existed);

                foreach($report_table_data as $report_time => $report_time_data){

                    foreach ($report_time_data as $location_uid => $location_data) {

                        foreach ($location_data as $venue_uid => $venue_data) {

                            foreach ($venue_data as $zone_uid => $dwell_data) {


                                if($zone_uid == 'nozone') $zone_uid = NULL;



                                $omy_report['total_dwell'] = ($dwell_data['total_dwell']['add'] ?? 0) - ($dwell_data['total_dwell']['minus'] ?? 0);
                                $omy_report['total_dwell_wifi'] = ($dwell_data['total_dwell_wifi']['add'] ?? 0) - ($dwell_data['total_dwell_wifi']['minus'] ?? 0);

                                $omy_report['total_dwell_ble'] = ($dwell_data['total_dwell_ble']['add'] ?? 0) - ($dwell_data['total_dwell_ble']['minus'] ?? 0);


                                $omy_report['total_dwell_engaged'] = ($dwell_data['total_dwell_engaged']['add'] ?? 0) - ($dwell_data['total_dwell_engaged']['minus'] ?? 0);
                                $omy_report['total_dwell_engaged_wifi'] = ($dwell_data['total_dwell_engaged_wifi']['add'] ?? 0) - ($dwell_data['total_dwell_engaged_wifi']['minus'] ?? 0);

                                $omy_report['total_dwell_engaged_ble'] = ($dwell_data['total_dwell_engaged_ble']['add'] ?? 0) - ($dwell_data['total_dwell_engaged_ble']['minus'] ?? 0);
                               


                                foreach (['15', '30', '60', '120', '240', '480', 'more'] as $temp_dwell) {
                                    $omy_report["dwell_{$temp_dwell}"]      = ($dwell_data["dwell_{$temp_dwell}"]['add'] ?? 0) - ($dwell_data["dwell_{$temp_dwell}"]['minus'] ?? 0);
                                    $omy_report["dwell_{$temp_dwell}_wifi"] = ($dwell_data["dwell_{$temp_dwell}_wifi"]['add'] ?? 0) - ($dwell_data["dwell_{$temp_dwell}_wifi"]['minus'] ?? 0);
                                    $omy_report["dwell_{$temp_dwell}_ble"]  = ($dwell_data["dwell_{$temp_dwell}_ble"]['add'] ?? 0) - ($dwell_data["dwell_{$temp_dwell}_ble"]['minus'] ?? 0);
                                }




                                $query = "SELECT id FROM {$report_table} WHERE tenant_id = '{$tenant->tenant_id}' AND report_date = '{$report_time}' AND location_uid = '{$location_uid}' AND venue_uid = '{$venue_uid}'";
                                $query .= empty($zone_uid) ? " " : " AND zone_uid = '{$zone_uid}'";
                                $query .= " LIMIT 1";


                                $omy_record = \DB::select(\DB::raw($query));
                                
                                unset($query);


                                if(!empty($omy_record)) {
                                        
                                    // Update record
                                    $query = "UPDATE {$report_table} SET ";

                                    $temp_query['total'] = count($omy_report);
                                    $i = 1;



                                    foreach ($omy_report as $col_name => $value_name) {

                                        $query .= " {$col_name} = {$col_name} + {$value_name} " . ($i == $temp_query['total'] ? " , updated_at = NOW() WHERE id = '{$omy_record[0]->id}' " : " , ");

                                        $i++;
                                    }

                                    \DB::update($query);
                                    unset($query, $omy_report, $col_name, $value_name, $i, $temp_query);

                                }else {


                                    $location = OmayaLocation::select('name')->withoutGlobalScopes()->where('tenant_id', $tenant->tenant_id)->where('location_uid', $location_uid)->first();
                                    $venue = OmayaVenue::select('name')->withoutGlobalScopes()->where('tenant_id', $tenant->tenant_id)->where('venue_uid', $venue_uid)->first();
                                    $zone = OmayaZone::select('name')->withoutGlobalScopes()->where('tenant_id', $tenant->tenant_id)->where('zone_uid', $zone_uid)->first();

                                    $omy_report_dwell = new ReportDwell;
                                    $omy_report_dwell->setTable($report_table);
                                    $omy_report_dwell->withoutGlobalScopes()->create(
                                        array_merge([
                                        'tenant_id'         => $tenant->tenant_id,  
                                        'report_date'       => $report_time, 
                                        'location_uid'      => $location_uid, 
                                        'venue_uid'         => $venue_uid, 
                                        'zone_uid'          => $zone_uid,
                                        'location_name'     => $location->name,
                                        'venue_name'        => $venue->name,
                                        'zone_name'         => $zone ? $zone ->name : NULL,
                                        ], $omy_report)
                                    );


                                }
                            }
                        }
                    }
                }
            }


            ###############################

            

        });

        
        $this->info('Omaya Report dwell successfully run.');

        $omy_time['end'] = date('Y-m-d H:i:s');
        $omy_cache->hMSet("OMAYA:SCHEDULER:REPORT:DWELL:RUN_AT", $omy_time);
        
        $omy_cache->close();

    }

   

}
