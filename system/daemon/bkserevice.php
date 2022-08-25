<?php
###################### START ################################
# Checking service. only 1 service can run at a time. if not, duplicate data
#############################################################
$omy_service = exec('ps aux | grep omaya_service.php | grep -v grep | wc -l');

if($omy_service > 1) {
        
    echo "Fail to start service. Service already running. \nPlease stop the existing service first. \n\n***** NOTE *****\n Run 'systemctl stop omaya_service' OR \n Kill the process id --check pid by 'ps aux | grep omaya_service.php'\n****************\n\n";
    die(customLogger(LOG_PATH, "Fail to start service. Service already running. Please stop the existing service first"));
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


define("LOG_PATH", "/daemon/omaya_service");


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

    // echo "MQTT fail to connect. at host : ". getenv('MQTT_HOST') ."\n";
    customLogger(LOG_PATH, "MQTT fail to connect. at host : ". getenv('MQTT_HOST'));

}

#############################
### END SUBSCRIBE TOPIC
#############################
// End code 



function processData($topic, $message){

    // FROM OMAYA AGENT
    $omy_data = json_decode(trim($message), true);


    var_dump($omy_data['payload']);

    if (!is_array($omy_data)) return;


    // Checking REQUIRED data first 
    if(!isset($omy_data['mac_address_ap']) && !isset($omy_data['mac_address_device']) && !isset($omy_data['rssi'])) {

        // echo "No required data pass from AP \n";
        customLogger(LOG_PATH, "No required data pass from AP");
        return;

    }


    $omy_cache = new Redis();
    $omy_cache->connect(getenv('REDIS_HOST'), getenv('REDIS_PORT'));

    $process_uid = uniqid() . time() . rand(99,999) . uniqid() . md5($omy_data['mac_address_device']);

    $omy_cache->set("AGENT:DATA:{$process_uid}", $message);
    $omy_cache->expire("AGENT:DATA:{$process_uid}", 300);

    shell_exec("nohup /usr/bin/php /var/www/omaya/system/daemon/omaya_service_process.php $process_uid 1>/dev/null 2>&1 &");
   


    unset($omy_data);


    $omy_db->close();
    $omy_cache->close();
    unset($omy_db, $omy_cache);
    return;

}



?>
