<?php

namespace App\Console\Commands;

use App\Console\Commands\OmayaBlacklist;
use App\Models\OmayaCloud;
use App\Models\OmayaDeviceList;
use App\Models\OmayaDeviceTracker;
use Illuminate\Console\Command;

class OmayaBlacklist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'omaya:blacklist';

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
        

        $tenants = OmayaCloud::get()->each(function($tenant) use($omy_cache) {

            // $mac_address = \DB::select(\DB::raw("SELECT mac_address_device, total_count, updated_at FROM omaya_device_counts WHERE tenant_id = '{$tenant->tenant_id}' AND updated_at  > (NOW() - INTERVAL  1 DAY)"));


                // \DB::delete("UPDATE omaya_device_counts set total_count = 0 WHERE tenant_id = '{$tenant->tenant_id}' AND  updated_at  < (NOW() - INTERVAL  1 DAY) AND total_count <= 3");



            // foreach ($mac_address as $key => $value) {


            //     \DB::raw("DELETE FROM omaya_device_counts WHERE tenant_id = '{$tenant->tenant_id}' AND mac_address_device = '{$value['mac_address_device']}'");



            // }

        });

        $this->info('Omaya Blacklist successfully run.');


        $omy_time['end'] = date('Y-m-d H:i:s');
        $omy_cache->hMSet("OMAYA:SCHEDULER:OMAYA:BLACKLIST:RUN_AT", $omy_time);
        $omy_cache->close();
        
    }
}
