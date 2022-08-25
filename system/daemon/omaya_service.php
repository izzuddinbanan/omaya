<?php
###################### START ################################
# Checking service. only 1 service can run at a time. if not, duplicate data
#############################################################
$omy_service = exec('ps aux | grep omaya_service.php | grep -v grep | wc -l');

if($omy_service > 1) {
        
    die("Fail to start service. Service already running. \nPlease stop the existing service first. \n\n***** NOTE *****\n Run 'systemctl stop omaya_service' OR \n Kill the process id --check pid by 'ps aux | grep omaya_service.php'\n****************\n\n");
}
unset($omy_service);
###################### END #################################


// MQTT Library
require_once dirname(__FILE__, 2)."/tools/php_mqtt.php";

### CUSTOM LOAD .env FILE  #####
require_once dirname(__FILE__, 2)."/tools/DotEnv.php";
(new DotEnv(dirname(__FILE__, 3)."/.env"))->load();


// Base function
require_once dirname(__FILE__, 2) . "/tools/sql.helper.php";
require_once dirname(__FILE__, 2) . "/tools/constant.php";
require_once dirname(__FILE__, 3) . "/app/Supports/helper.php";




###########################
#### MQTT SUBSCRIBE TOPIC
###########################


$omy_mqtt = new Bluerhinos\phpMQTT(getenv('MQTT_HOST'), getenv('MQTT_PORT'), uniqid() . time() . rand(10,99));
if ($omy_mqtt->connect(true, NULL, getenv('MQTT_USER'), getenv('MQTT_PASSWORD'))) {

    $omy_mqtt->debug = false;

    // SUBSCRIBE TOPIC and call function processData
    $topics['DEVICE/AP/DATA'] = array('qos' => 0, 'function' => 'processData');
    $omy_mqtt->subscribe($topics, 0);


    while($omy_mqtt->proc()) {}



} else {

    customLogger(LOG_PATH_GENERAL, "[omaya_service] MQTT fail to connect. at host : ". getenv('MQTT_HOST'));

}

#############################
### END SUBSCRIBE TOPIC
#############################


function processData($topic, $message){



    $omy_data = json_decode(trim($message), true);


    if (!is_array($omy_data)) return;


    if(!isset($omy_data['mac_address_ap']) && !isset($omy_data['mac_address_device']) && !isset($omy_data['rssi'])) return;



    $omy_cache = new Redis();
    $omy_cache->connect(getenv('REDIS_HOST'), getenv('REDIS_PORT'));
    
    ################################################
    # CHECK DEVICE IS BLACKLIST OR NOT
    ################################################
    $omy_blacklist = $omy_cache->get("OMAYA:BLACKLIST:{$omy_data['mac_address_device']}");
    if(!empty($omy_blacklist)) return;

    unset($omy_blacklist);
    ########################END###############################



    ################################################
    # GET DEVICE [AP]
    ################################################
    $omy_ap = $omy_cache->hGetAll("DEVICE:AP:DATA:{$omy_data['mac_address_ap']}");


    if (empty($omy_ap)) {

        $omy_cache->set("OMAYA:JOB:AP-GET:{$omy_data["mac_address_ap"]}", $omy_data["mac_address_ap"], 30);
        customLogger("agent", "AP : {$omy_data['mac_address_ap']} | DEV : {$omy_data['mac_address_device']} = Get AP-GET from Database");

        return;
    }

    if ($omy_ap['dummy'] == "1" || $omy_ap['dummy'] == true) return;

    if($omy_ap['is_active'] == false) return;
    ########################END###############################




    customLogger($omy_ap['tenant_id'], "AP : {$omy_data['mac_address_ap']} | DEV : {$omy_data['mac_address_device']} = Received packet data [{$omy_data['device_type']} - {$omy_data['rssi_type']}] at {$omy_data['time']}.");




    ################################################
    # Filter mac address that use by device AP
    ################################################
    $omy_check_device = $omy_cache->hGetAll("DEVICE:AP:DATA:{$omy_data['mac_address_device']}");

    // Ignore if the mac_address_device is belong to device_controller_ap
    if(!empty($omy_check_device)) return;

    unset($omy_check_device);
    ########################END###############################




    ###################### START ###############
    # CHECKING TENANT LICENSE 
    ############################################
    $omy_license = $omy_cache->hGetAll("TENANT:LICENSE:{$omy_ap['tenant_id']}");

    if(empty($omy_license)) {

        $omy_cache->set("OMAYA:JOB:TENANT-LICENSE:{$omy_ap["tenant_id"]}", $omy_ap["tenant_id"], 30);
        customLogger("agent", "AP : {$omy_data['mac_address_ap']} | DEV : {$omy_data['mac_address_device']} = Get TENANT-LICENSE from license file");

        return;
        
    }

    if ($omy_license['dummy'] == "1" || $omy_license['dummy'] == true) {

        customLogger($omy_ap['tenant_id'], "AP : {$omy_data['mac_address_ap']} | DEV : {$omy_data['mac_address_device']} = License not valid.");
        return;
    
    }
    ########################END###############################




    ###################### START ###############
    # GET DATA TENANT
    ############################################

    $omy_tenant = $omy_cache->hGetAll("TENANT:DATA:{$omy_ap['tenant_id']}");

    if (empty($omy_tenant)) {

        $omy_cache->set("OMAYA:JOB:TENANT-GET:{$omy_ap["tenant_id"]}", $omy_ap["tenant_id"], 30);
        customLogger("agent", "AP : {$omy_data['mac_address_ap']} | DEV : {$omy_data['mac_address_device']} = Get TENANT-GET from Database");

        return;

    }

    if ($omy_tenant['dummy'] == "1" || $omy_tenant['dummy'] == true) return;


    if ($omy_tenant['is_active'] == "0") return;

    ########################END###############################




    ###################### START ###############
    # CHECK LIMIT DEVICE AP
    ############################################

    $omy_count = $omy_cache->hGetAll("AP:COUNT:{$omy_ap['tenant_id']}");

    if (empty($omy_count)) {

        $omy_cache->set("OMAYA:JOB:AP-COUNT:{$omy_ap["tenant_id"]}", $omy_ap["tenant_id"], 30);
        customLogger($omy_ap['tenant_id'], "AP : {$omy_data['mac_address_ap']} | DEV : {$omy_data['mac_address_device']} = Get AP-COUNT from Database");

    }


    if ($omy_count['dummy'] == true) return;


    if ($omy_count['ocount'] > $omy_license['device_limit']) {

        customLogger($omy_ap['tenant_id'], "AP : {$omy_data['mac_address_ap']} | DEV : {$omy_data['mac_address_device']} = Device [AP] reach max limit. Please use other license or delete device [AP].");
        return;

    }

    ########################END###############################


    // Update omaya_device_ap status
    $omy_cache->set("OMAYA:JOB:AP-UPDATE:{$omy_data["mac_address_ap"]}", $omy_data["mac_address_ap"], 30);



    ###################### START ###############
    # CHECK IF DEVICE IS REGISTERED
    ############################################
    $omy_tracker = $omy_cache->hGetAll("DEVICE:TRACKER:DATA:{$omy_ap['tenant_id']}:{$omy_data['mac_address_device']}");

    $is_registered = true;
    if (empty($omy_tracker)) $is_registered = false;

    unset($omy_tracker);
    ########################END###############################



    ###################### START ###############
    # CHECK IF LICENSE iS NOT CROWD AND NOT REGISTERED SHOULD NOT CAPTURE DATA
    ############################################    
    if($omy_license['type'] != "crowd" && $is_registered == false) return;




    $omy_data['rssi_type'] = empty($omy_data['rssi_type']) ? "wifi" :  $omy_data['rssi_type'];


    ###################### START ###############
    # STORE TOTAL PACKET RECEIVE FROM AP
    ############################################
    $temp_date = date("Y-m-d H:00:00", strtotime($omy_data['time']));
    $omy_cache->incr("AP:PACKET-TOTAL:{$omy_current_data['mac_address_ap']}:{$omy_data['rssi_type']}:{$temp_date}");
    unset($temp_date);
    ########################END###############################



    // Bypass for register device
    if($is_registered == false) {


        ###################### START ###############
        # FILTER RANDOM MAC ADDRESS
        ############################################
        if($omy_tenant['is_filter_mac_random']) {

            // Refer https://www.mist.com/get-to-know-mac-address-randomization-in-2020/#:~:text=Fortunately%20it%20is%20easy%20to,it%20is%20a%20randomized%20address.
            if(in_array($omy_data['mac_address_device'][1], ['2', '6', 'A', 'E'])) return;



        }
        ###########################END############################



        ###################### START ###############
        # FILTER OUI MAC ADDRESS
        ############################################
        $omy_filter_mac = filterOuiMacAddress($omy_cache, $omy_data['mac_address_device']);
        if($omy_tenant['is_filter_oui'] && $omy_data['rssi_type'] == 'wifi') {

            if($omy_filter_mac['status'] == false) return;

        }

        ###########################END############################

    }




    customLogger("{$omy_ap['tenant_id']}", "AP : {$omy_data['mac_address_ap']} | DEV : {$omy_data['mac_address_device']} = RSSI : {$omy_data['rssi']} , Accept rssi <= ". ($omy_data['rssi_type'] == 'wifi' ?  abs($omy_ap['rssi_min']) : abs($omy_ap['rssi_min_ble'])));
        


    // Check rssi and check type rssi wether BLE or WIFI
    if( abs($omy_data['rssi']) <=  ($omy_data['rssi_type'] == 'wifi' ?  abs($omy_ap['rssi_min']) : abs($omy_ap['rssi_min_ble']))) {



        $omy_current_id = $omy_cache->get("RAWDATA:CURRENT_ID");

        if($omy_current_id == false) {
            $omy_current_id = time();
            $omy_cache->set("RAWDATA:CURRENT_ID", $omy_current_id);
        }

        $omy_redis_clear = 300;
    

        ###################### START ###############
        # STORE RSSI
        ############################################
        $omy_prev_rssi = $omy_cache->get("RAWDATA:RSSI:{$omy_current_id}:{$omy_ap['mac_address']}:{$omy_data['mac_address_device']}:{$omy_ap['tenant_id']}");

        $omy_cache->set("RAWDATA:RSSI:{$omy_current_id}:{$omy_ap['mac_address']}:{$omy_data['mac_address_device']}:{$omy_ap['tenant_id']}", (abs($omy_data['rssi']) + abs($omy_prev_rssi)), $omy_redis_clear);

        unset($omy_prev_rssi);



        ###################### START ###############
        # STORE ALL DEVICE
        ############################################

        $unique_device = $omy_cache->get("RAWDATA:DEVICE:{$omy_current_id}:{$omy_ap['tenant_id']}");
        
        if($unique_device)
            $unique_device = json_decode($unique_device, true);
        else
            $unique_device = [];

       
        $unique_device = array_merge([$omy_data['mac_address_device'] => true], $unique_device);
        $unique_device = json_encode($unique_device);


        $omy_cache->set("RAWDATA:DEVICE:{$omy_current_id}:{$omy_ap['tenant_id']}", $unique_device, $omy_redis_clear);
        unset($unique_device);




        ###################### START ###############
        # COUNT DEVICE DATA
        ############################################
        $cur_count = $omy_cache->get("RAWDATA:COUNT:{$omy_current_id}:{$omy_ap['mac_address']}:{$omy_data['mac_address_device']}") + 1;

        #COUNT
        $omy_cache->set("RAWDATA:COUNT:{$omy_current_id}:{$omy_ap['mac_address']}:{$omy_data['mac_address_device']}", $cur_count, $omy_redis_clear);



        ###################### START ###############
        # STORE ALL DEVICE [AP]
        ############################################
        $unique_ap = $omy_cache->get("RAWDATA:AP:{$omy_current_id}:{$omy_ap['tenant_id']}");


        if($unique_ap)
            $unique_ap = json_decode($unique_ap, true);
        else
            $unique_ap = [];

        $unique_ap = array_merge([$omy_ap['mac_address'] => true], $unique_ap);
        $unique_ap = json_encode($unique_ap);

        $omy_cache->set("RAWDATA:AP:{$omy_current_id}:{$omy_ap['tenant_id']}", $unique_ap, $omy_redis_clear);
        // $omy_cache->expire("RAWDATA:AP:{$omy_current_id}:{$omy_ap['tenant_id']}", $omy_redis_clear);
        unset($unique_ap);



        ###################### START ###############
        # STORE RAW DATA
        ############################################
        $omy_temp = [];
        $omy_temp['tenant_id']          = $omy_ap['tenant_id'];
        $omy_temp['mac_address_ap']     = $omy_ap['mac_address'];
        $omy_temp['mac_address_device'] = $omy_data['mac_address_device'];
        $omy_temp['device_vendor']      = empty($omy_filter_mac['vendor']) ? NULL : $omy_filter_mac['vendor'];
        $omy_temp['rssi']               = $omy_data['rssi'];
        $omy_temp['rssi_type']          = $omy_data['rssi_type'];
        $omy_temp['seen_at']            = $omy_data['time'];
        $omy_temp['location_uid']       = $omy_ap['location_uid'];
        $omy_temp['venue_uid']          = $omy_ap['venue_uid'];
        $omy_temp['zone_uid']           = $omy_ap['zone_uid'];
        $omy_temp['created_at']         = date("Y-m-d H:i:s");
        $omy_temp['payload']            = $omy_data['payload'];
        // $omy_temp['raw_data']           = $omy_data;



        $omy_prev_raw = $omy_cache->get("RAWDATA:DATA:{$omy_current_id}:{$omy_data['mac_address_ap']}:{$omy_data['mac_address_device']}:{$omy_ap['tenant_id']}");


        if($omy_prev_raw)
            $omy_prev_raw = json_decode($omy_prev_raw, true);
        else
            $omy_prev_raw = [];


        $omy_prev_raw = array_replace_recursive($omy_prev_raw, $omy_temp);
        $omy_prev_raw = json_encode($omy_prev_raw);

        $omy_cache->set("RAWDATA:DATA:{$omy_current_id}:{$omy_data['mac_address_ap']}:{$omy_data['mac_address_device']}:{$omy_ap['tenant_id']}", $omy_prev_raw, $omy_redis_clear);
        unset($omy_temp, $omy_prev_raw, $omy_redis_clear);




        customLogger($omy_ap['tenant_id'], "AP : {$omy_data['mac_address_ap']} | DEV : {$omy_data['mac_address_device']} = [{$omy_data['device_type']}-{$omy_data['rssi_type']}] Success store data into redis for next processing [ {$omy_data['time']} ]");

    }

    unset($omy_data, $omy_ap, $omy_license, $omy_tenant, $omy_filter_mac, $omy_temp, $omy_first);


    $omy_cache->close();
    unset($omy_cache);


    // $omy_conn_workspace = new swoole_client(SWOOLE_SOCK_UDP, SWOOLE_SOCK_SYNC);
    // $omy_conn_workspace->connect('127.0.0.1', 8999);

    // if($omy_conn_workspace->isconnected()){

    //     $omy_conn_workspace->send($message);
    //     $omy_conn_workspace->close();
    // }

    // return;
    

}



?>
