<?php

###################### START ################################
# Checking service. only 1 service can run at a time. if not, duplicate data
#############################################################
$omy_service = exec('ps aux | grep omaya_agent_mikrotik_ble.php | grep -v grep | wc -l');

if($omy_service > 1) {
        
    die("Fail to start service. Service already running. \nPlease stop the existing service first. \n\n***** NOTE *****\n Run 'systemctl stop omaya_agent_mikrotik_ble' OR \n Kill the process id --check pid by 'ps aux | grep omaya_agent_mikrotik_ble.php'\n****************\n\n");
}
###################### END #################################



// MQTT Library
require_once dirname(__FILE__, 3)."/tools/php_mqtt.php";

### CUSTOM LOAD .env FILE  #####
require_once dirname(__FILE__, 3)."/tools/DotEnv.php";
(new DotEnv(dirname(__FILE__, 4)."/.env"))->load();


// Base function
require_once dirname(__FILE__, 3) . "/tools/sql.helper.php";
require_once dirname(__FILE__, 3) . "/tools/constant.php";
require_once dirname(__FILE__, 4) . "/app/Supports/helper.php";




###########################
#### MQTT SUBSCRIBE TOPIC
###########################


$omy_mqtt = new Bluerhinos\phpMQTT(getenv('MQTT_HOST'), getenv('MQTT_PORT'), uniqid() . time() . rand(10,99));
if ($omy_mqtt->connect(true, NULL, getenv('MQTT_USER'), getenv('MQTT_PASSWORD'))) {

    $omy_mqtt->debug = false;

    // SUBSCRIBE TOPIC and call function processData
    $topics['mikrotik'] = array('qos' => 0, 'function' => 'processData');
    $omy_mqtt->subscribe($topics, 0);


    while($omy_mqtt->proc()) {}



} 
#############################
### END SUBSCRIBE TOPIC
#############################


function processData($topic, $message){


    $omy_data = json_decode(trim($message), true);
    if (!is_array($omy_data)) return;

    foreach ( $omy_data['locs']['0']['tags'] as $value ){
    

        $omy_temp = [];

        $omy_temp['mac_address_ap']     = $omy_data['locs']['0']['id'];
        $omy_temp['mac_address_device'] = $value['id'];

        $omy_temp['rssi']               = $value['rssi'];
        $omy_temp['time']               = date("Y-m-d H:i:s");//date("Y-m-d H:i:s", hexdec(bin2hex(substr($omy_data, 18, 4))));
        $omy_temp['device_type']        = "mikrotik";
        $omy_temp['rssi_type']          = "ble"; // BLE or WIFI


        $omy_mqtt = new Bluerhinos\phpMQTT(getenv('MQTT_HOST'), getenv('MQTT_PORT'), uniqid() . time() . rand(10,99));

        if ($omy_mqtt->connect(true, NULL, getenv('MQTT_USER'), getenv('MQTT_PASSWORD'))) {


            // remove all char in mac addres
            $omy_temp['mac_address_ap']     = str_replace([':', '-', ' ', '.'], "", $omy_temp['mac_address_ap']);
            $omy_temp['mac_address_device'] = str_replace([':', '-', ' ', '.'], "", $omy_temp['mac_address_device']);


            $omy_mqtt->publish('DEVICE/AP/DATA', json_encode($omy_temp), 0, false);
            $omy_mqtt->close();



        }else {

            customLogger(LOG_PATH_GENERAL, "[mikrotik-ble] Fail to connect MQTT");

        }

        ############################### END #############################
        ##
        ## MQTT FUNCTION --  ALL AGENT WILL PUSH TO MQTT
        ##
        #################################################################


        unset($omy_data, $omy_temp, $omy_mqtt);


    }






}



?>
