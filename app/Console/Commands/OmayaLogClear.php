<?php

namespace App\Console\Commands;

use App\Models\OmayaCloud;
use Illuminate\Console\Command;

class OmayaLogClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'omaya:log-clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To clear all log after certain of period time';

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

            $omy_data['path'] = "/var/www/omaya/storage/";

            if (file_exists("{$omy_data['path']}/logs/")) {


                if (file_exists("{$omy_data['path']}/logs/{$tenant->tenant_id}/")) {

                    //convert days to minutes 
                    $min = ($tenant->delete_log ?? 10) * 24 * 60;

                    system("find {$omy_data['path']}/logs/{$tenant->tenant_id}/ -mmin +{$min} -type f -delete");


                } 
            }

            ################# END PROCESSING DATA #################


        });


         $this->info('Omaya Log Clear successfully run.');

        $omy_time['end'] = date('Y-m-d H:i:s');
        $omy_cache->hMSet("OMAYA:SCHEDULER:LOG:CLEAR:RUN_AT", $omy_time);
        
        $omy_cache->close();
    }
}
