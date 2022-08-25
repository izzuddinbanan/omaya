<?php

###################### START ################################
# Checking service. only 1 service can run at a time. if not, duplicate data
#############################################################
$omy_service = exec('ps aux | grep omaya_agent_cambium_wifi.php | grep -v grep | wc -l');

if($omy_service > 1) {
        
    die("Fail to start service. Service already running. \nPlease stop the existing service first. \n\n***** NOTE *****\n Run 'systemctl stop omaya_agent_cambium_wifi' OR \n Kill the process id with port 9005 --check pid by 'netstat -tulpn | grep 9005'\n****************\n\n");
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



$omy_swoole['port'] 	 		= 9005;
$omy_swoole['daemonize'] 		= 1;
$omy_swoole['pid_file'] 		= "/run/omaya-agent-cambium-wifi.pid";
$omy_swoole["debug_data"]		= false; //true or false . Enable this for debug


// $omy_server = new swoole_server('0.0.0.0', $omy_swoole['port'], SWOOLE_PROCESS, SWOOLE_SOCK_UDP);
// $omy_server = new swoole_server("127.0.0.1", $omy_swoole['port']);
$omy_server = new Swoole\Http\Server("0.0.0.0", 9005);

$omy_config = ["worker_num" => 8, "max_conn" => 1024];


$omy_server->set(array(
    'worker_num'    => $omy_config["worker_num"] ?? 8,
    'daemonize'     => $omy_swoole['daemonize'],
    'max_conn'      => $omy_config["max_conn"] ?? 1024,
    'max_request'   => 0,
    'pid_file'      => $omy_swoole['pid_file'],
    'group'         => 'nginx',
    'user'          => 'nginx',
    // 'ssl_cert_file' => "/etc/ssl/certs/nginx-selfsigned.crt",
    // 'ssl_key_file'  => "/etc/ssl/private/nginx-selfsigned.key",
    // 'ssl_ciphers'   => 'HIGH:!aNULL:!MD5',
    // 'open_tcp_keepalive' => true,
));


$omy_server->on("request", function ($request, $response) {


    go(function () use ($request, $response) {

        $response->header("Content-Type", "application/json");

        $omy_data = json_decode($request->rawContent(), true);
    
        // var_dump($omy_data);
        // return;

        if (is_array($omy_data)){

            $omy_temp = [];

            if(empty($omy_data['probe_requests_clients'])) return;


            foreach ($omy_data['probe_requests_clients'] as $key => $value) {

                $omy_temp['mac_address_ap']     = strtoupper(str_replace(["-", ":"], "", $omy_data['ap_mac']));
                $omy_temp['mac_address_device'] = strtoupper(str_replace(["-", ":"], "", $value['mac']));
                $omy_temp['time']               = date("Y-m-d H:i:s");
                $omy_temp['rssi']               = $value['rssi'];
                $omy_temp['device_type']        = "cambium";
                $omy_temp['rssi_type']          = "wifi"; // BLE or WIFI


                ############################### START #############################
                ##
                ## MQTT FUNCTION --  ALL AGENT WILL PUSH TO MQTT
                ##
                ###################################################################

                


                $omy_mqtt = new Bluerhinos\phpMQTT(getenv('MQTT_HOST'), getenv('MQTT_PORT'), uniqid() . time() . rand(10,99));

                if ($omy_mqtt->connect(true, NULL, getenv('MQTT_USER'), getenv('MQTT_PASSWORD'))) {
                    
                    $omy_mqtt->publish('DEVICE/AP/DATA', json_encode($omy_temp), 0, false);
                    $omy_mqtt->close();

                } else {

                    customLogger(LOG_PATH_AGENT, "[cambium] MQTT [wifi] fail to connect.");

                }

                ############################### END #############################
                ##
                ## MQTT FUNCTION --  ALL AGENT WILL PUSH TO MQTT
                ##
                #################################################################


            }

            unset($omy_data, $omy_temp, $omy_mqtt);

        }else{


            $omy_result = array("reply:Reply-Message" => "Unknown / unsupported function.");
            $response->status(403);
            $response->end(json_encode($omy_result));

        }
            
        return;

    });
    

});


$omy_server->start();


