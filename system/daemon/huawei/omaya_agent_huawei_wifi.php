<?php

###################### START ################################
# Checking service. only 1 service can run at a time. if not, duplicate data
#############################################################
$omy_service = exec('ps aux | grep omaya_agent_huawei_wifi.php | grep -v grep | wc -l');

if($omy_service > 1) {
        
    die("Fail to start service. Service already running. \nPlease stop the existing service first. \n\n***** NOTE *****\n Run 'systemctl stop omaya_agent_huawei_wifi' OR \n Kill the process id with port 9000 --check pid by 'netstat -tulpn | grep 9000'\n****************\n\n");
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



$kiw_swoole['port'] 	 		= 9000;
$kiw_swoole['daemonize'] 		= 1;
$kiw_swoole['pid_file'] 		= "/run/omaya-agent-huawei-wifi.pid";
$kiw_swoole["debug_data"]		= false; //true or false . Enable this for debug


$omy_server = new swoole_server('0.0.0.0', $kiw_swoole['port'], SWOOLE_PROCESS, SWOOLE_SOCK_UDP);

$omy_config = ["worker_num" => 6, "max_conn" => 2048];


$omy_server->set(array(
    'worker_num'    => $omy_config["worker_num"] ?? 12,
    'daemonize'     => $kiw_swoole['daemonize'],
    'max_conn'      => $omy_config["max_conn"] ?? 1024,
    'max_request'   => 0,
    'pid_file'      => $kiw_swoole['pid_file']
));


$omy_server->on('Packet', function ($omy_server = "", $omy_data = "", $omy_address = "") {

    
    go(function () use ($omy_data) {



        $omy_report_data['date_time']    = date("Y-m-d H:i:s");
        $omy_report_data['device_count'] = hexdec(bin2hex(substr($omy_data, 9, 1)));


        // ap mac address
        $omy_report_data['mac_address_ap'] = strtoupper(bin2hex(substr($omy_data, 3, 6)));



        if ($omy_report_data['device_count'] > 0){

            
            $omy_pointer = 12;


            for ($omy_x = 0; $omy_x < $omy_report_data['device_count']; $omy_x++){


                $omy_temp = [];

                $omy_temp['mac_address_ap']     = strtoupper($omy_report_data['mac_address_ap']);
                $omy_temp['mac_address_device'] = strtoupper(bin2hex(substr($omy_data, $omy_pointer + 2, 6)));
                $omy_temp['rssi']               = bin8dec(decbin(hexdec(bin2hex(substr($omy_data, $omy_pointer + 14, 1)))));
                $omy_temp['time']               = $omy_report_data['date_time'];
                $omy_temp['device_type']        = "huawei";
                $omy_temp['rssi_type']          = "wifi"; // BLE or WIFI
                $omy_temp['channel']            = hexdec(bin2hex(substr($omy_data, $omy_pointer + 12, 1)));
                $omy_temp['noise_floor']        = bin8dec(decbin(hexdec(bin2hex(substr($omy_data, $omy_pointer + 15, 1)))));

               
                // get the flags to determine next data position
                $omy_flag           = sprintf("%08b", ord(substr($omy_data, $omy_pointer + 13, 1)));
                $omy_option_length  = hexdec(bin2hex(substr($omy_data, $omy_pointer + 16, 1)));


                // increase counter to mark next data
                $omy_pointer += (substr($omy_flag, -1) == "1") ? (17 + $omy_option_length) : 16;




                // $omy_cache = new Redis();
                // $omy_cache->connect(getenv('REDIS_HOST'), getenv('REDIS_PORT'));
    

                // $omy_tracker = $omy_cache->hGetAll("DEVICE:TRACKER:DATA:vivo:{$omy_temp['mac_address_device']}");

                // $omy_cache->close();


                // customLogger("", "WIFI :: AP : {$omy_temp['mac_address_ap']} | DEVICE  : {$omy_temp['mac_address_device']} [ {$omy_tracker['name']} ] | TIME : {$omy_temp['time']}");


                // if(empty($omy_tracker)) continue;
                // if($omy_tracker == false) continue;
                


                // customLogger("", "WIFI :: AP : {$omy_temp['mac_address_ap']} | DEVICE  : {$omy_temp['mac_address_device']} [ {$omy_tracker['name']} ] | PUSH MQTT");

                            
                ############################### START #############################
                ##
                ## MQTT FUNCTION --  ALL AGENT WILL PUSH TO MQTT
                ##
                ###################################################################

                $omy_mqtt = new Bluerhinos\phpMQTT(getenv('MQTT_HOST'), getenv('MQTT_PORT'), uniqid() . time() . rand(10,99));

                if ($omy_mqtt->connect(true, NULL, getenv('MQTT_USER'), getenv('MQTT_PASSWORD'))) {

                    // remove all char in mac addres
                    $omy_temp['mac_address_ap']     = str_replace([':', '-', ' ', '.'], "", $omy_temp['mac_address_ap']);
                    $omy_temp['mac_address_device'] = str_replace([':', '-', ' ', '.'], "", $omy_temp['mac_address_device']);

                    $omy_mqtt->publish('DEVICE/AP/DATA', json_encode($omy_temp), 0, false);
                    $omy_mqtt->close();

                } else {

                    customLogger(LOG_PATH_GENERAL, "[huawei-wifi] Fail to connect MQTT");

                }

                ############################### END #############################
                ##
                ## MQTT FUNCTION --  ALL AGENT WILL PUSH TO MQTT
                ##
                #################################################################



            }


        }

        unset($omy_data, $omy_report_data, $omy_pointer, $omy_x, $omy_flag, $omy_option_length, $omy_temp, $omy_mqtt);

    });

    return;

});


$omy_server->start();


