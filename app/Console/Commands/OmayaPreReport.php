<?php

namespace App\Console\Commands;

use App\Models\OmayaCloud;
use App\Models\OmayaLocation;
use App\Models\OmayaReportDwell;
use App\Models\OmayaReportGeneral;
use App\Models\OmayaReportHeatmap;
use App\Models\OmayaVenue;
use App\Models\OmayaZone;
use Illuminate\Console\Command;

class OmayaPreReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'omaya:pre-report';

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


        foreach (["omaya_report_dwell", "omaya_report_general", "omaya_report_heatmap"] as $report) {

            $report_time  = date("Y-m-d H:00:00", strtotime('+1 hour'));
            $report_table = "{$report}_" . date("Ym", strtotime($report_time));


            $omy_existed = $omy_cache->get("TABLE_CHECKED:{$report_table}");

            if (empty($omy_existed)) {

                $omy_existed = \DB::select(\DB::raw("SELECT COUNT(*) AS ocount FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = '{$report_table}' AND TABLE_SCHEMA = 'omaya'"))[0];


                if ($omy_existed->ocount == 0) \DB::select(\DB::raw("CREATE TABLE {$report_table} LIKE {$report}_templates"));

                $omy_cache->set("TABLE_CHECKED:{$report_table}", "checked");
            }


            $tenants = OmayaCloud::get()->each(function($tenant) use($omy_cache, $report_table, $report_time, $report) {



                foreach(OmayaVenue::withoutGlobalScopes()->where('tenant_id', $tenant->tenant_id)->get() as $venue){


                    $zones = \DB::select("SELECT name, zone_uid FROM omaya_zones WHERE tenant_id = '{$tenant->tenant_id}' AND venue_uid = '{$venue->venue_uid}'");
                    


                    if(empty($zones)) {

                        $query = "SELECT id FROM {$report_table} WHERE tenant_id = '{$tenant->tenant_id}' AND report_date = '{$report_time}' AND location_uid = '{$venue->location_uid}' AND venue_uid = '{$venue->venue_uid}' LIMIT 1";

                        $omy_record = \DB::select(\DB::raw($query));



                        if($report == "omaya_report_dwell")
                            $omy_report = new OmayaReportDwell;

                        if($report == "omaya_report_general")
                            $omy_report = new OmayaReportGeneral;

                        if($report == "omaya_report_heatmap")
                            $omy_report = new OmayaReportHeatmap;


                        $omy_report->setTable($report_table);


                        if(empty($omy_record)) {


                            $location = OmayaLocation::select('name')->withoutGlobalScopes()->where('tenant_id', $tenant->tenant_id)->where('location_uid', $venue->location_uid)->first();


                            $omy_report->withoutGlobalScopes()->create([
                                'tenant_id'         => $tenant->tenant_id,  
                                'report_date'       => $report_time, 
                                'location_uid'      => $venue->location_uid, 
                                'venue_uid'         => $venue->venue_uid, 
                                'zone_uid'          => NULL,
                                'location_name'     => $location->name,
                                'venue_name'        => $venue->name,
                                'zone_name'         => NULL,
                            ]);

                        }
                    }else {


                        foreach($zones as $zone) {

                            $query = "SELECT id FROM {$report_table} WHERE tenant_id = '{$tenant->tenant_id}' AND report_date = '{$report_time}' AND location_uid = '{$venue->location_uid}' AND venue_uid = '{$venue->venue_uid}' AND zone_uid  = '{$zone->zone_uid}' LIMIT 1";

                            $omy_record = \DB::select(\DB::raw($query));


                            if($report == "omaya_report_dwell")
                                $omy_report = new OmayaReportDwell;

                            if($report == "omaya_report_general")
                                $omy_report = new OmayaReportGeneral;

                            if($report == "omaya_report_heatmap")
                                $omy_report = new OmayaReportHeatmap;

                            $omy_report->setTable($report_table);

                            if(empty($omy_record)) {


                                $location = OmayaLocation::select('name')->withoutGlobalScopes()->where('tenant_id', $tenant->tenant_id)->where('location_uid', $venue->location_uid)->first();


                                $omy_report->withoutGlobalScopes()->create([
                                    'tenant_id'         => $tenant->tenant_id,  
                                    'report_date'       => $report_time, 
                                    'location_uid'      => $venue->location_uid, 
                                    'venue_uid'         => $venue->venue_uid, 
                                    'zone_uid'          => $zone->zone_uid,
                                    'location_name'     => $location->name,
                                    'venue_name'        => $venue->name,
                                    'zone_name'         => $zone->name,
                                ]);

                            }


                        }




                    }

                }
                

            });
        }
        
        $this->info('Omaya Pre Report successfully run.');

        $omy_time['end'] = date('Y-m-d H:i:s');
        $omy_cache->hMSet("OMAYA:SCHEDULER:OMAYA:PRE-REPORT:RUN_AT", $omy_time);
        $omy_cache->close();
    }
}
