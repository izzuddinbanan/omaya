<?php

namespace App\Console\Commands;

use App\Models\OmayaCloud;
use App\Models\OmayaLocation;
use App\Models\OmayaReportCrossVisit as ReportCrossVisit;
use App\Models\OmayaVenue;
use App\Models\OmayaZone;
use Illuminate\Console\Command;

class OmayaReportCrossVisit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:cross-visit';

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

            \DB::update("UPDATE omaya_raw_massages SET cross_visit_report_status = '{$omy_processing_id}' WHERE tenant_id = '{$tenant->tenant_id}' AND cross_visit_report_status = 'pending'");

            ###########################


            $controllers = \DB::select("SELECT mac_address_ap FROM omaya_raw_massages WHERE tenant_id = '{$tenant->tenant_id}' AND cross_visit_report_status = '{$omy_processing_id}' GROUP BY mac_address_ap");



            $omy_visits = [];
            foreach ($controllers as $controller){


                // Get device_controller AP Data
                $omy_ap = $omy_cache->hGetAll("DEVICE:AP:DATA:{$controller->mac_address_ap}");
                if (empty($omy_ap)) {

                    $omy_ap = OmayaDeviceController::withoutGlobalScopes()->where('mac_address', $controller->mac_address_ap)->get()->toArray();

                    if (empty($omy_ap)) continue;
                    else $omy_cache->hMSet("DEVICE:AP:DATA:{$controller->mac_address_ap}", $omy_ap);

                }
                if(empty($omy_ap["zone_uid"])) $omy_ap["zone_uid"] = "nozone"; 




                // dd($omy_previous_ap);



                $raws = \DB::select("SELECT * FROM omaya_raw_massages WHERE tenant_id = '{$tenant->tenant_id}' AND cross_visit_report_status = '{$omy_processing_id}' AND mac_address_ap = '{$controller->mac_address_ap}'");

                

                \DB::update("UPDATE omaya_raw_massages SET cross_visit_report_status = 'completed'  WHERE tenant_id = '{$tenant->tenant_id}' AND cross_visit_report_status = '{$omy_processing_id}' AND mac_address_ap = '{$controller->mac_address_ap}'");




                foreach ($raws as $raw) {


                    $report_table = "omaya_report_cross_visit_" . date("Ym", strtotime($raw->report_time));


                    if($report_table == "omaya_report_cross_visit_197001")
                        dd($raw);

                    $omy_previous_ap = $omy_cache->hGetAll("DEVICE:AP:DATA:{$raw->previous_mac_address_ap}");
                    if (empty($omy_previous_ap)) {

                        $omy_previous_ap = OmayaDeviceController::withoutGlobalScopes()->where('mac_address', $raw->previous_mac_address_ap)->get()->toArray();

                        if (empty($omy_previous_ap)) continue;
                        else $omy_cache->hMSet("DEVICE:AP:DATA:{$raw->previous_mac_address_ap}", $omy_previous_ap);

                    }
                    if(empty($omy_previous_ap["zone_uid"])) $omy_previous_ap["zone_uid"] = "nozone"; 


                    $omy_visits[$report_table][$raw->report_time][$omy_previous_ap['location_uid']][$omy_previous_ap['venue_uid']][$omy_previous_ap['zone_uid']]["before_" . $omy_ap['location_uid']]["before_" . $omy_ap['venue_uid']]["before_" . $omy_ap['zone_uid']][$raw->rssi_type] = ($omy_visits[$report_table][$raw->report_time][$omy_previous_ap['location_uid']][$omy_previous_ap['venue_uid']][$omy_previous_ap['zone_uid']]["before_" . $omy_ap['location_uid']]["before_" . $omy_ap['venue_uid']]["before_" . $omy_ap['zone_uid']][$raw->rssi_type] ?? 0) + 1;



                }


            }

            unset($raw, $raws, $controllers, $controller);



            foreach($omy_visits as $report_table => $report_table_data){



                $omy_existed = $omy_cache->get("TABLE_CHECKED:{$report_table}");

                if (empty($omy_existed)) {


                    $omy_existed = \DB::select(\DB::raw("SELECT COUNT(*) AS ocount FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = '{$report_table}' AND TABLE_SCHEMA = 'omaya'"))[0];


                    if ($omy_existed->ocount == 0) \DB::select(\DB::raw("CREATE TABLE {$report_table} LIKE omaya_report_cross_visit_templates"));

                    $omy_cache->set("TABLE_CHECKED:{$report_table}", "checked");
                }

                unset($omy_existed);

                foreach($report_table_data as $report_time => $report_time_data){

                    foreach ($report_time_data as $location_uid => $location_data) {

                        foreach ($location_data as $venue_uid => $venue_data) {

                            foreach ($venue_data as $zone_uid => $old_location) {


                                foreach ($old_location as $old_location_uid => $old_venue) {

                                    foreach ($old_venue as $old_venue_uid => $old_zone) {

                                        foreach ($old_zone as $old_zone_uid => $count) {



                                            if($zone_uid == 'nozone') $zone_uid = NULL;
                                            if($old_zone_uid == 'before_nozone') $old_zone_uid = NULL;



                                            $old_location_uid = str_replace("before_", "", $old_location_uid);
                                            $old_venue_uid    = str_replace("before_", "", $old_venue_uid);

                                            if($old_zone_uid)
                                            $old_zone_uid     = str_replace("before_", "", $old_zone_uid);

                                                
                                            $omy_report["total_ble"]  = $count["ble"] ?? 0 ;
                                            $omy_report["total_wifi"] = $count["wifi"] ?? 0 ;
                                            $omy_report["total"]      = $omy_report["total_ble"] + $omy_report["total_wifi"];

                                            unset($count);



                                            $query = "SELECT id FROM {$report_table} WHERE tenant_id = '{$tenant->tenant_id}' AND report_date = '{$report_time}' AND location_uid = '{$old_location_uid}' AND venue_uid = '{$old_venue_uid}' ";
                                            $query .= empty($old_zone_uid) ? " " : " AND zone_uid = '{$old_zone_uid}'";


                                            $query .= " AND to_location_uid = '{$location_uid}' AND to_venue_uid = '{$venue_uid}' ";

                                            $query .= empty($zone_uid) ? " " : " AND to_zone_uid = '{$zone_uid}' ";

                                            $query .= " LIMIT 1";


                                            $omy_record = \DB::select(\DB::raw($query));
                                            
                                            unset($query);

                                            if(!empty($omy_record)) {
                                                    
                                                // Update record
                                                $query = "UPDATE {$report_table} SET total = total + {$omy_report['total']}, total_wifi = total_wifi + {$omy_report['total_wifi']}, total_ble = total_ble + {$omy_report['total_ble']}";

                                                \DB::update($query);
                                                unset($query, $omy_report);

                                            }else {

                                                $old_location = OmayaLocation::select('name')->withoutGlobalScopes()->where('tenant_id', $tenant->tenant_id)->where('location_uid', $old_location_uid)->first();
                                                $old_venue = OmayaVenue::select('name')->withoutGlobalScopes()->where('tenant_id', $tenant->tenant_id)->where('venue_uid', $old_venue_uid)->first();
                                                $old_zone = OmayaZone::select('name')->withoutGlobalScopes()->where('tenant_id', $tenant->tenant_id)->where('zone_uid', $old_zone_uid)->first();

                                                $to_location = OmayaLocation::select('name')->withoutGlobalScopes()->where('tenant_id', $tenant->tenant_id)->where('location_uid', $location_uid)->first();
                                                $to_venue = OmayaVenue::select('name')->withoutGlobalScopes()->where('tenant_id', $tenant->tenant_id)->where('venue_uid', $venue_uid)->first();
                                                $to_zone = OmayaZone::select('name')->withoutGlobalScopes()->where('tenant_id', $tenant->tenant_id)->where('zone_uid', $zone_uid)->first();


                                                $omy_report_cross = new ReportCrossVisit;
                                                $omy_report_cross->setTable($report_table);
                                                $omy_report_cross->withoutGlobalScopes()->create(
                                                    array_merge([
                                                    'tenant_id'         => $tenant->tenant_id,  
                                                    'report_date'       => $report_time, 
                                                    'location_uid'      => $old_location_uid, 
                                                    'venue_uid'         => $old_venue_uid, 
                                                    'zone_uid'          => $old_zone_uid,
                                                    'to_location_uid'   => $location_uid, 
                                                    'to_venue_uid'      => $venue_uid, 
                                                    'to_zone_uid'       => $zone_uid,
                                                    'location_name'     => $old_location->name,
                                                    'venue_name'        => $old_venue->name,
                                                    'zone_name'         => $old_zone ? $old_zone ->name : NULL,
                                                    'to_location_name'  => $to_location->name,
                                                    'to_venue_name'     => $to_venue->name,
                                                    'to_zone_name'      => $to_zone ? $to_zone ->name : NULL,
                                                    ], $omy_report)
                                                );

                                            }

                                        }

                                    }

                                }
                               
                            }
                        }
                    }
                }
            }


            ###############################

            

        });

        
        $this->info('Omaya Report Cross Visit successfully run.');

        $omy_time['end'] = date('Y-m-d H:i:s');
        $omy_cache->hMSet("OMAYA:SCHEDULER:REPORT:CROSS-VISIT:RUN_AT", $omy_time);
        
        $omy_cache->close();



    }
}
