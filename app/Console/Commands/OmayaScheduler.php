<?php

namespace App\Console\Commands;

use App\Models\OmayaCloud;
use App\Models\OmayaDeviceController;
use App\Models\OmayaDeviceTracker;
use App\Models\OmayaReportDwell;
use Illuminate\Console\Command;

class OmayaScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'omaya:main';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Omaya Main Scheduler';

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
        $tenants = OmayaCloud::get()->each(function($tenant) {
        

            ################################################
            #  UPDATE STATUS DEVICE AP
            ################################################

            $devices = \DB::select(\DB::raw("SELECT id, last_seen_at FROM omaya_device_controllers WHERE (last_seen_at < DATE_SUB(NOW(), INTERVAL 60 MINUTE) OR last_seen_at IS NULL) AND status = 'active' AND tenant_id = '{$tenant->tenant_id}'"));

            foreach ($devices as $key => $device) {

                if($device->last_seen_at) {

                    $status = "no new packet";

                }else {


                    $status = "offline";

                }

                OmayaDeviceController::withoutGlobalScopes()->where('id', $device->id)->update([
                    'status' => $status
                ]);

            }

            unset($devices, $device, $key, $status);

            #####################END#######################



            ################################################
            #  UPDATE STATUS DEVICE TRACKER
            ################################################

            $devices = \DB::select(\DB::raw("SELECT id, last_seen_at FROM omaya_device_trackers WHERE (last_seen_at < DATE_SUB(NOW(), INTERVAL 60 MINUTE) OR last_seen_at IS NULL) AND status = 'active' AND tenant_id = '{$tenant->tenant_id}'"));

            foreach ($devices as $key => $device) {

                if($device->last_seen_at) {

                    $status = "no new packet";

                }else {


                    $status = "offline";

                }

                OmayaDeviceTracker::withoutGlobalScopes()->where('id', $device->id)->update([
                    'status' => $status
                ]);

            }

            unset($devices, $device, $key, $status);

            #####################END#######################




            ################################################
            #  REMOVE RAW DATA MASSAGE LESS THAN CONFIG REMOVE DWELL
            ################################################

            if($tenant->is_filter_dwell_time) {


                \DB::delete("DELETE FROM omaya_raw_massages WHERE (last_seen_at  < (NOW() - INTERVAL  900 SECOND) AND last_seen_at < (first_seen_at + INTERVAL '{$tenant->remove_dwell_time}' SECOND))  AND tenant_id = '{$tenant->tenant_id}' ");


            }else {

                // Remove dwell yang 0 
                \DB::delete("DELETE FROM omaya_raw_massages WHERE first_seen_at = last_seen_at AND last_seen_at  < (NOW() - INTERVAL  900 SECOND) AND tenant_id = '{$tenant->tenant_id}'");


            }

            \DB::delete("DELETE FROM omaya_raw_massages WHERE last_seen_at  < (NOW() - INTERVAL  960 SECOND) AND tenant_id = '{$tenant->tenant_id}'");
            \DB::delete("DELETE FROM omaya_raw_massages WHERE mac_address_ap IS NULL");
            \DB::delete("DELETE FROM omaya_raw_massages WHERE first_seen_at = '0000-00-00 00:00:00' OR last_seen_at = '0000-00-00 00:00:00'");

            \DB::delete("DELETE FROM omaya_device_histories WHERE location_uid IS NULL");

            \DB::delete("DELETE FROM omaya_device_histories WHERE report_date < (NOW() - INTERVAL  3 DAY)");







        }); //End loop all tenant



        $this->info('Omaya Main scheduler successfully run.');


        $omy_time['end'] = date('Y-m-d H:i:s');
        $omy_cache->hMSet("OMAYA:SCHEDULER:OMAYA:MAIN:RUN_AT", $omy_time);
        $omy_cache->close();
    }


    
}
