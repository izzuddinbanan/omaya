<?php 

###################### START ################################
# Checking service. 
#############################################################

if(!isset($argv[1])) die("No current ID for process. Please insert ID.\n");


require_once dirname(__FILE__, 2)."/tools/DotEnv.php";
(new DotEnv(dirname(__FILE__, 3)."/.env"))->load();


// Base function
require_once dirname(__FILE__, 2) . "/tools/sql.helper.php";
require_once dirname(__FILE__, 2) . "/tools/constant.php";
require_once dirname(__FILE__, 3) . "/app/Supports/helper.php";

define("LOG_PATH", "/daemon/omaya_extract");


$omy_db = mysqlConnection();
if ($omy_db->connect_errno) return;

$omy_cache = new Redis();
$omy_cache->connect(getenv('REDIS_HOST'), getenv('REDIS_PORT'));


$omy_current_id = $omy_cache->get("RAWDATA:CURRENT_ID");
if($omy_current_id == $argv[1] ) die("Current ID is still running. please delete RAWDATA:CURRENT_ID in redis.\n");

$omy_current_id = $argv[1];



do {


    // zone setting

    $device_keys = $omy_cache->scan($omy_scan_device, "RAWDATA:DEVICE:{$omy_current_id}:*");

    foreach($device_keys as $device_key) {


        $tenant_id         = end(explode(":", $device_key)); 

        $mac_address_device_arr = $omy_cache->get($device_key);
        if(empty($mac_address_device_arr)) continue;


        $mac_address_device_arr = json_decode($mac_address_device_arr, true);

        foreach($mac_address_device_arr as $mac_address_device  => $none_val) {

            $temp['min_rssi'] = '99999';
            customLogger($tenant_id, "--------- Get nearest AP for data device : {$mac_address_device} ----------------", 'rssi');


            // GET LIST OF DEVICE [AP]
            $mac_address_ap_arr = $omy_cache->get("RAWDATA:AP:{$omy_current_id}:{$tenant_id}");
            $mac_address_ap_arr = json_decode($mac_address_ap_arr, true);

            if(empty($mac_address_ap_arr)) continue;


            foreach($mac_address_ap_arr as $mac_address_ap => $none_val2) {


                $rssi_count   = abs($omy_cache->get("RAWDATA:COUNT:{$omy_current_id}:{$mac_address_ap}:{$mac_address_device}"));
                $total_rssi   = abs($omy_cache->get("RAWDATA:RSSI:{$omy_current_id}:{$mac_address_ap}:{$mac_address_device}:{$tenant_id}"));
                    

                if($rssi_count <= 0 || $total_rssi <= 0) continue;

                $temp['cal_rssi'] = $total_rssi / $rssi_count;
                $temp['cal_rssi'] = number_format((float)$temp['cal_rssi'], 2, '.', '');


                $temp_log['ap'] = $omy_cache->hGetAll("DEVICE:AP:DATA:{$mac_address_ap}");

                if ($temp_log['ap']) $temp_log['ap'] = $temp_log['ap']['name'];
                else $temp_log['ap'] = "-";

                customLogger($tenant_id, "AP : {$mac_address_ap}  [ {$temp_log['ap']} ] | DEV : {$mac_address_device} | RSSI : {$temp['cal_rssi']}", 'rssi');

                if($temp['cal_rssi'] == 0) continue;



                // Find smallest rssi between many AP
                if($temp['cal_rssi'] <= $temp['min_rssi']) {


                    $raw_data = $omy_cache->get("RAWDATA:DATA:{$omy_current_id}:{$mac_address_ap}:{$mac_address_device}:{$tenant_id}");

                    if($raw_data == false) continue;

                    $raw_data = json_decode($raw_data, true);


                    $near['ap_name']            = $temp_log['ap'];
                    $near['tenant_id']          = $tenant_id;
                    $near['mac_address_ap']     = $mac_address_ap;
                    $near['mac_address_device'] = $mac_address_device;
                    $near['location_uid']       = $raw_data['location_uid'];
                    $near['venue_uid']          = $raw_data['venue_uid'];
                    $near['zone_uid']           = $raw_data['zone_uid'];
                    $near['rssi']               = $temp['cal_rssi'];
                    $near['raw_data']           = $raw_data;
                   
                    // overwrite
                    $temp['min_rssi'] = $temp['cal_rssi'];



                }

                unset($temp_log);


            } //end mac address ap



            // After end check device seen at all AP/Controller
            if(empty($near['tenant_id'])) continue;

            customLogger($near['tenant_id'], "--------- FOUND nearest AP for data device : {$mac_address_device} ----------------", 'rssi');
            customLogger($near['tenant_id'], "AP : {$near['mac_address_ap']} [ {$near['ap_name']} ] | DEV : {$near['mac_address_device']} | RSSI : {$near['rssi']} | TIME : {$near['raw_data']['seen_at']}", 'rssi');
            customLogger($near['tenant_id'], "--------- END  {$mac_address_device} ----------------", 'rssi');
            // customLogger($near['tenant_id'], "----------------------------------------------------------------------", 'rssi');



            ###################### START ###############
            # CHECK IF DEVICE IS REGISTERED
            ############################################
            $omy_tracker = $omy_cache->hGetAll("DEVICE:TRACKER:DATA:{$near['tenant_id']}:{$near['mac_address_device']}");

            ########################END###############################



            customLogger($near['tenant_id'], "AP : {$near['mac_address_ap']} | DEV : {$near['mac_address_device']} = " . (empty($omy_tracker) ? "Un" : "") . "Registered device ", 'rssi');

            if (!empty($omy_tracker) && $omy_tracker['is_active'] == true) {
              

                $cur_temp = ["mac_address_device"  => $near['mac_address_device'],
                    "mac_address_ap"               => $near['mac_address_ap'],
                    "rssi"                         => "-" . (int) $near['rssi'],
                    "last_detected"                => $near['raw_data']['seen_at'],
                    "location_uid"                 => $near['location_uid'],
                    "venue_uid"                    => $near['venue_uid'],
                    "zone_uid"                     => $near['zone_uid'] ?? NULL,
                    "tenant_id"                    => $near['tenant_id'],
                ];

                $omy_cache->hMSet("DEVICE:TRACKER:PRESENT:{$near['tenant_id']}:{$near['mac_address_device']}", $cur_temp);
                $omy_cache->expire("DEVICE:TRACKER:PRESENT:{$near['tenant_id']}:{$near['mac_address_device']}", 300);

                $omy_cache->hMSet("OMAYA:JOB:TRACKER-UPDATE:{$near["mac_address_device"]}", $cur_temp);
                $omy_cache->expire("OMAYA:JOB:TRACKER-UPDATE:{$near["mac_address_device"]}", 30);



                do {

                    $old_keys = $omy_cache->scan($omy_scan_venue, "VENUE:TRACKER:PRESENT:{$near['tenant_id']}:*:{$near['mac_address_device']}");


                    foreach ($old_keys as  $old_key) {

                        $omy_cache->del($old_key);
                    }

                }while ($omy_scan_venue != 0);

                unset($old_keys, $old_key, $omy_scan_venue);


                $omy_cache->set("VENUE:TRACKER:PRESENT:{$near['tenant_id']}:{$near['location_uid']}:{$near['venue_uid']}:{$near['zone_uid']}:{$near['mac_address_ap']}:{$near['mac_address_device']}", $cur_temp['rssi'], 300);
                // $omy_cache->expire("VENUE:TRACKER:PRESENT:{$near['tenant_id']}:{$near['location_uid']}:{$near['venue_uid']}:{$near['zone_uid']}:{$near['mac_address_ap']}:{$near['mac_address_device']}", 10);


                unset($cur_temp);
            }


            $omy_conn_prereport = new swoole_client(SWOOLE_TCP);
            $omy_conn_prereport->connect('127.0.0.1', 8999, 1);



            if($omy_conn_prereport->isconnected()){

                $omy_conn_prereport->send(json_encode($near));
                $omy_conn_prereport->close();

            }






        } // end device

        
                

    }

} while ($omy_scan_device != 0);


unset($mac_address_device, $mac_address_ap, $none_val, $temp);

do{

    $all_keys = $omy_cache->scan($omy_scan_keys, "*{$omy_current_id}*");

    // Delete all cache yang da massage
    foreach ($all_keys as $omy_key) {

        $omy_cache->del($omy_key);

    }

} while ($omy_scan_keys != 0);


$omy_cache->close();
$omy_db->close();
unset($omy_db, $omy_cache);

