<?php

namespace App\Console\Commands;

use App\Models\OmayaCloud;
use App\Models\OmayaLocation;
use App\Models\OmayaReportHeatmap as ReportHeatmap;
use App\Models\OmayaVenue;
use App\Models\OmayaZone;
use Illuminate\Console\Command;

class OmayaReportHeatmap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:heatmap';

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

            \DB::update("UPDATE omaya_raw_massages SET heatmap_report_status = '{$omy_processing_id}' WHERE tenant_id = '{$tenant->tenant_id}' AND heatmap_report_status = 'pending'");

            ###########################


            $controllers = \DB::select("SELECT mac_address_ap FROM omaya_raw_massages WHERE tenant_id = '{$tenant->tenant_id}' AND heatmap_report_status = '{$omy_processing_id}' GROUP BY mac_address_ap");



            $omy_heatmap = [];
            foreach ($controllers as $controller){


                // Get device_controller AP Data
                $omy_ap = $omy_cache->hGetAll("DEVICE:AP:DATA:{$controller->mac_address_ap}");
                if (empty($omy_ap)) {

                    $omy_ap = OmayaDeviceController::withoutGlobalScopes()->where('mac_address', $controller->mac_address_ap)->get()->toArray();

                    if (empty($omy_ap)) continue;
                    else $omy_cache->hMSet("DEVICE:AP:DATA:{$controller->mac_address_ap}", $omy_ap);

                }
                if(empty($omy_ap["zone_uid"])) $omy_ap["zone_uid"] = "nozone"; 




                $raws = \DB::select("SELECT * FROM omaya_raw_massages WHERE tenant_id = '{$tenant->tenant_id}' AND heatmap_report_status = '{$omy_processing_id}' AND mac_address_ap = '{$controller->mac_address_ap}'");

                

                \DB::update("UPDATE omaya_raw_massages SET heatmap_report_status = 'completed'  WHERE tenant_id = '{$tenant->tenant_id}' AND heatmap_report_status = '{$omy_processing_id}' AND mac_address_ap = '{$controller->mac_address_ap}'");



                foreach ($raws as $raw) {


                    $report_table = "omaya_report_heatmap_" . date("Ym", strtotime($raw->last_seen_at));
                    $report_date  = date('Y-m-d H:00:00', strtotime($raw->last_seen_at));


                    if($raw->is_heatmap_count == true) {

                        $omy_heatmap[$report_table][$report_date][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']]["total"] = ($omy_heatmap[$report_table][$report_date][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']]["total"] ?? 0) + 1;


                        $omy_heatmap[$report_table][$report_date][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']]["total_{$raw->rssi_type}"] = ($omy_heatmap[$report_table][$report_date][$omy_ap['location_uid']][$omy_ap['venue_uid']][$omy_ap['zone_uid']]["total_{$raw->rssi_type}"] ?? 0) + 1;


                    }


                }


            }




            unset($controllers, $controller, $raws, $raw);




            ################# START PROCESSING DATA #################


            foreach($omy_heatmap as $report_table => $report_table_data){



                $omy_existed = $omy_cache->get("TABLE_CHECKED:{$report_table}");

                if (empty($omy_existed)) {


                    $omy_existed = \DB::select(\DB::raw("SELECT COUNT(*) AS ocount FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = '{$report_table}' AND TABLE_SCHEMA = 'omaya'"))[0];


                    if ($omy_existed->ocount == 0) \DB::select(\DB::raw("CREATE TABLE {$report_table} LIKE omaya_report_heatmap_templates"));

                    $omy_cache->set("TABLE_CHECKED:{$report_table}", "checked");
                }

                unset($omy_existed);

                foreach($report_table_data as $report_time => $report_time_data){

                    foreach ($report_time_data as $location_uid => $location_data) {

                        foreach ($location_data as $venue_uid => $venue_data) {

                            foreach ($venue_data as $zone_uid => $heatmap_data) {


                                if($zone_uid == 'nozone') $zone_uid = NULL;


                                

                                $omy_report['total']      = $heatmap_data['total'];
                                $omy_report['total_wifi'] = $heatmap_data['total_wifi'] ?? 0;
                                $omy_report['total_ble']  = $heatmap_data['total_ble'] ?? 0;



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


                                    $omy_report_heatmap = new ReportHeatmap;
                                    $omy_report_heatmap->setTable($report_table);
                                    $omy_report_heatmap->withoutGlobalScopes()->create(
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


            ################# END PROCESSING DATA #################


        });
        
        $this->info('Omaya Report Heatmap successfully run.');

        $omy_time['end'] = date('Y-m-d H:i:s');
        $omy_cache->hMSet("OMAYA:SCHEDULER:REPORT:HEATMAP:RUN_AT", $omy_time);
        $omy_cache->close();
    }
}
