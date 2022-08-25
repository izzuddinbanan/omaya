<?php

###################### START ################################
# Checking service. only 1 service can run at a time. if not, duplicate data
#############################################################
$omy_service = exec('ps aux | grep omaya_job.php | grep -v grep | wc -l');

if($omy_service > 1) {
        
    echo "Fail to start service. Service already running. \nPlease stop the existing service first. \n\n***** NOTE *****\n Run 'systemctl stop omaya_job' OR \n Kill the process id --check pid by 'ps aux | grep omaya_job.php'\n****************\n\n";
    die(customLogger(LOG_PATH, "Fail to start service. Service already running. Please stop the existing service first"));
}
unset($omy_service);
###################### END #################################

### CUSTOM LOAD .env FILE  #####
require_once dirname(__FILE__, 2)."/tools/DotEnv.php";
(new DotEnv(dirname(__FILE__, 3)."/.env"))->load();


// Base function
require_once dirname(__FILE__, 2) . "/tools/sql.helper.php";
require_once dirname(__FILE__, 2) . "/tools/constant.php";
require_once dirname(__FILE__, 3) . "/app/Supports/helper.php";


define("LOG_PATH", "/daemon/omaya_extract");


function run() {

    $omy_cache = new Redis();
    $omy_cache->connect(getenv('REDIS_HOST'), getenv('REDIS_PORT'));


    $omy_db = mysqlConnection();

    if ($omy_db->connect_errno) {
        return;
    }  
    $omy_it = null;


    do {


        // zone setting

        $omy_keys = $omy_cache->scan($omy_it, "OMAYA:JOB:*");

        foreach ($omy_keys as $key => $omy_key) {

            $temp_key = explode(":", $omy_key);


            if($temp_key[2] == "RESTART-SERVICE") {

                $service   = $omy_cache->get($omy_key);

                shell_exec("systemctl restart {$service} > /dev/null 2>/dev/null &");
                shell_exec("/usr/bin/php /var/www/omaya/artisan omaya:service  > /dev/null 2>/dev/null &");

                unset($service);


            }else if($temp_key[2] == "AP-UPDATE"){


                $mac_address_ap   = $omy_cache->get($omy_key);

                $omy_db->query("UPDATE omaya_device_controllers SET last_seen_at = NOW(), status = 'active' WHERE mac_address = '{$mac_address_ap}' LIMIT 1");


                unset($mac_address_ap);

            }else if($temp_key[2] == "AP-GET") {


                $mac_address_ap  = $omy_cache->get($omy_key);

                $omy_ap = $omy_db->query("SELECT SQL_CACHE * FROM omaya_device_controllers WHERE mac_address = '{$mac_address_ap}' LIMIT 1");

                $omy_ap = $omy_ap->fetch_all(MYSQLI_ASSOC)[0];

                if (empty($omy_ap)) $omy_ap = array("dummy" => true);
                
                $omy_cache->hMSet("DEVICE:AP:DATA:{$mac_address_ap}", $omy_ap);

                unset($mac_address_ap, $omy_ap);


            }else if ($temp_key[2] == "TENANT-LICENSE") {



                $tenant_id  = $omy_cache->get($omy_key);

                $omy_license = getTenantLicense($tenant_id, false, "/var/www/omaya/public/storage/tenants/{$tenant_id}/tenant.license");
                

                if(empty($omy_license) || $omy_license["result"] == false) $omy_license = array("dummy" => true);

                $omy_cache->hMSet("TENANT:LICENSE:{$tenant_id}", $omy_license);

                unset($omy_license, $tenant_id);


            }else if ($temp_key[2] == "TENANT-GET") {



                $tenant_id  = $omy_cache->get($omy_key);

                $omy_tenant = $omy_db->query("SELECT SQL_CACHE * FROM omaya_clouds WHERE tenant_id = '{$tenant_id}' LIMIT 1");

                $omy_tenant = $omy_tenant->fetch_all(MYSQLI_ASSOC)[0];


                if (empty($omy_tenant)) $omy_tenant = array("dummy" => true);
                
                $omy_cache->hMSet("TENANT:DATA:{$tenant_id}", $omy_tenant);

                unset($tenant_id, $omy_tenant);


            }else if ($temp_key[2] == "AP-COUNT") {
                

                $tenant_id  = $omy_cache->get($omy_key);

                $omy_count = $omy_db->query("SELECT SQL_CACHE count(id) AS ocount FROM omaya_device_controllers WHERE tenant_id = '{$tenant_id}'");

                $omy_count = $omy_count->fetch_all(MYSQLI_ASSOC)[0];


                if (empty($omy_count)) $omy_count = array("dummy" => true);
                
                $omy_cache->hMSet("AP:COUNT:{$tenant_id}", $omy_count);

                unset($omy_count, $tenant_id);

            }else if ($temp_key[2] == "TRACKER-UPDATE") {


                $tracker_data   = $omy_cache->hGetAll($omy_key);

                $sql_zone = " , last_zone_uid = '{$tracker_data['zone_uid']}' ";
                if(empty($tracker_data["zone_uid"])) $sql_zone = " , last_zone_uid = NULL ";


                $omy_db->query("UPDATE omaya_device_trackers SET last_seen_at = NOW(), 
                    last_location_uid = '{$tracker_data['location_uid']}', 
                    last_venue_uid = '{$tracker_data['venue_uid']}' 
                    {$sql_zone} , 
                    status = 'active',
                    last_rssi = '{$tracker_data['rssi']}'  
                    WHERE mac_address = '{$tracker_data["mac_address_device"]}' AND 
                    tenant_id = '{$tracker_data["tenant_id"]}' LIMIT 1");

            }


            $omy_cache->del($omy_key);
            unset($temp_key);


        }




    } while ($omy_it != 0);


    $omy_db->close();

    $omy_cache->close();
   

}

// Every 0.5s, execute the run function
Swoole\Timer::tick(1000, "run");