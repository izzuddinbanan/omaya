<?php

namespace App\Console\Commands;

use App\Models\OmayaCloud;
use App\Models\OmayaDeviceController;
use App\Models\OmayaReportDeviceController as ReportDeviceController;
use Illuminate\Console\Command;

class OmayaReportDeviceController extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:device-controller';

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


            $controllers = \DB::select("SELECT mac_address FROM omaya_device_controllers WHERE tenant_id = '{$tenant->tenant_id}'");

            $omy_device = [];
            foreach ($controllers as $controller) {


                \DB::update("UPDATE omaya_raw_massages SET device_controller_report_status = '{$omy_processing_id}' WHERE tenant_id = '{$tenant->tenant_id}' AND device_controller_report_status = 'pending' AND mac_address_ap = '{$controller->mac_address}'");


                $raws = \DB::select("SELECT * FROM omaya_raw_massages WHERE tenant_id = '{$tenant->tenant_id}' AND device_controller_report_status = '{$omy_processing_id}' AND mac_address_ap = '{$controller->mac_address}'");


                // $packet_total[$omy_ap['mac_address']] = $omy_cache->get("RAWDATA:TOTAL-PACKET:{$controller->mac_address}");
                // $omy_cache->del("RAWDATA:TOTAL-PACKET:{$controller->mac_address}");

                foreach ($raws as $raw) {

                    $report_table = "omaya_report_device_controller_" . date("Ym", strtotime($raw->first_seen_at));
                    $report_date  = date('Y-m-d H:00:00', strtotime($raw->first_seen_at));
                    
                    $omy_device[$report_table][$report_date][$controller->mac_address]["packet_accept_{$raw->rssi_type}"] = ($omy_device[$report_table][$report_date][$controller->mac_address]["packet_accept_{$raw->rssi_type}"] ?? 0) + 1;
                    
                }

                \DB::update("UPDATE omaya_raw_massages SET device_controller_report_status = 'completed'  WHERE tenant_id = '{$tenant->tenant_id}' AND device_controller_report_status = '{$omy_processing_id}' AND mac_address_ap = '{$controller->mac_address}'");


                foreach ($omy_cache->keys("AP:PACKET-TOTAL:{$controller->mac_address}:*") as $omy_key) {

                    $omy_key_arr = explode(":", $omy_key, 5);


                    $report_table = "omaya_report_device_controller_" . date("Ym", strtotime($omy_key_arr[4]));
                    $report_date  = $omy_key_arr[4];


                    $temp_packet_total = $omy_cache->get($omy_key);

                    $omy_device[$report_table][$report_date][$controller->mac_address]["packet_total_{$omy_key_arr[3]}"] = ($omy_cache->get($omy_key) ?? 0) + 4;
                    
                    // $omy_cache->del($omy_key);



                }


        
                

            }

            // dd($omy_device);

            ################# START PROCESSING DATA #################


            foreach($omy_device as $report_table => $report_table_data){



                $omy_existed = $omy_cache->get("TABLE_CHECKED:{$report_table}");

                if (empty($omy_existed)) {


                    $omy_existed = \DB::select(\DB::raw("SELECT COUNT(*) AS ocount FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = '{$report_table}' AND TABLE_SCHEMA = 'omaya'"))[0];


                    if ($omy_existed->ocount == 0) \DB::select(\DB::raw("CREATE TABLE {$report_table} LIKE omaya_report_device_controller_templates"));

                    $omy_cache->set("TABLE_CHECKED:{$report_table}", "checked");
                }

                unset($omy_existed);

                foreach($report_table_data as $report_time => $report_time_data){

                    foreach ($report_time_data as $mac_address_ap => $device_data) {

                        $query = "SELECT id FROM {$report_table} WHERE tenant_id = '{$tenant->tenant_id}' AND report_date = '{$report_time}' AND mac_address_ap = '{$mac_address_ap}'";
                        $query .= empty($zone_uid) ? " " : " AND zone_uid = '{$zone_uid}'";
                        $query .= " LIMIT 1";


                        $omy_record = \DB::select(\DB::raw($query));
                        
                        unset($query);

                        $omy_report["packet_total_ble"] = $device_data["packet_total_ble"] ?? 0; 
                        $omy_report["packet_accept_ble"] = $device_data["packet_accept_ble"] ?? 0;

                        $omy_report["packet_total_wifi"] = $device_data["packet_total_wifi"] ?? 0; 
                        $omy_report["packet_accept_wifi"] = $device_data["packet_accept_wifi"] ?? 0;


                        $omy_report["packet_total"] = $omy_report["packet_total_ble"] + $omy_report["packet_total_wifi"]; 
                        $omy_report["packet_accept"] = $omy_report["packet_accept_ble"] + $omy_report["packet_accept_wifi"];


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

                        }else {


                            $omy_report_device = new ReportDeviceController;
                            $omy_report_device->setTable($report_table);
                            $omy_report_device->withoutGlobalScopes()->create(
                                array_merge([
                                'tenant_id'         => $tenant->tenant_id,  
                                'report_date'       => $report_time, 
                                'mac_address_ap'    => $mac_address_ap, 
                                ], $omy_report)
                            );

                        }
                    }


                }
            }


            ################# END PROCESSING DATA #################


        });


         $this->info('Omaya Report Device Controller successfully run.');

        $omy_time['end'] = date('Y-m-d H:i:s');
        $omy_cache->hMSet("OMAYA:SCHEDULER:REPORT:DEVICE-CONTROLLER:RUN_AT", $omy_time);
        
        $omy_cache->close();

    }
}
