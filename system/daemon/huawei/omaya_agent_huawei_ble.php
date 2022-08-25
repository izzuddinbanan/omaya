<?php

###################### START ################################
# Checking service. only 1 service can run at a time. if not, duplicate data
#############################################################
$omy_service = exec('ps aux | grep omaya_agent_huawei_ble.php | grep -v grep | wc -l');

if($omy_service > 1) {
        
    die("Fail to start service. Service already running. \nPlease stop the existing service first. \n\n***** NOTE *****\n Run 'systemctl stop omaya_agent_huawei_ble' OR \n Kill the process id with port 9001 --check pid by 'netstat -tulpn | grep 9001'\n****************\n\n");
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


$kiw_swoole['port']             = 9001;
$kiw_swoole['daemonize']        = 1;
$kiw_swoole['pid_file']         = "/run/omaya-agent-huawei-ble.pid";
$kiw_swoole["debug_data"]       = false; //true or false . Enable this for debug



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


        $omy_temp = [];

        $omy_temp['mac_address_ap'] 	= strtoupper(bin2hex(substr($omy_data, 3, 6)));
        $omy_temp['mac_address_device'] = strtoupper(bin2hex(substr($omy_data, 12, 6)));


        $omy_temp['rssi']               = bin8dec(decbin(hexdec(bin2hex(substr($omy_data, 22, 1)))));
        $omy_temp['time']               = date("Y-m-d H:i:s");//date("Y-m-d H:i:s", hexdec(bin2hex(substr($omy_data, 18, 4))));
        $omy_temp['device_type']        = "huawei";
        $omy_temp['rssi_type']          = "ble"; // BLE or WIFI


        $omy_temp['packet_id']          = bin2hex(substr($omy_data, 26, 3));
        $omy_temp['kontak_id']          = bin2hex(substr($omy_data, 30, 3));
        $omy_temp['data_type']          = bin2hex(substr($omy_data, 33, 1));



        $omy_payload = substr($omy_data, 34);

        $omy_counter = 0;

        $omy_temp["payload"] = [];  
        $omy_result = [];

        $omy_payload_length = strlen($omy_payload);


        while ($omy_counter <= $omy_payload_length) {


            // get the length of payload

            $omy_current_length = hexdec(bin2hex(substr($omy_payload, $omy_counter, 1)));


            // increase counter so we pointing to the data

            $omy_counter++;


            // get the payload data up to specific length

            $omy_current_data = bin2hex(substr($omy_payload, $omy_counter, $omy_current_length));


            // point to the next data

            $omy_counter += $omy_current_length;


            // check the data and update accordingly

            $omy_current_data = str_split($omy_current_data, 2);


            if ($omy_current_data[0] == "01") {


                $omy_result['timestamp'] = hexdec("{$omy_current_data[1]}{$omy_current_data[2]}{$omy_current_data[3]}{$omy_current_data[4]}");

                $omy_result['battery_level'] = hexdec($omy_current_data[5]);


            } elseif ($omy_current_data[0] == "02") {


                $omy_result['sensitivity'] = hexdec($omy_current_data[1]);

                $omy_result['x_val'] = hexdec($omy_current_data[2]);
                $omy_result['y_val'] = hexdec($omy_current_data[3]);
                $omy_result['z_val'] = hexdec($omy_current_data[4]);


                if ("{$omy_current_data[5]}{$omy_current_data[6]}" != "ffff") {

                    $omy_result['last_dt'] = hexdec($omy_current_data[5]) + (hexdec($omy_current_data[6]) * 256);

                }


                $omy_result['last_move'] = hexdec($omy_current_data[7]) + (hexdec($omy_current_data[8]) * 256);


            } elseif ($omy_current_data[0] == "05") {


                $omy_result['light_percentage'] = hexdec($omy_current_data[1]);
                $omy_result['temperature'] = hexdec($omy_current_data[2]);


            } elseif ($omy_current_data[0] == "06") {


                $omy_result['sensitivity'] = $omy_current_data[1];

                $omy_result['x_val'] = hexdec($omy_current_data[2]);
                $omy_result['y_val'] = hexdec($omy_current_data[3]);
                $omy_result['z_val'] = hexdec($omy_current_data[4]);


            } elseif ($omy_current_data[0] == "07") {


                if ("{$omy_current_data[1]}{$omy_current_data[2]}" != "ffff") {

                    $omy_result['last_dt'] = hexdec($omy_current_data[1]) + (hexdec($omy_current_data[2]) * 256);

                }


            } elseif ($omy_current_data[0] == "08") {


                if ("{$omy_current_data[1]}{$omy_current_data[2]}" != "ffff") {

                    $omy_result['last_dt'] = hexdec($omy_current_data[1]) + (hexdec($omy_current_data[2]) * 256);

                }


            } elseif ($omy_current_data[0] == "0a") {


                $omy_result['light_percentage'] = hexdec($omy_current_data[1]);


            } elseif ($omy_current_data[0] == "0b") {


                $omy_result['temperature'] = hexdec($omy_current_data[1]);


            } elseif ($omy_current_data[0] == "0c") {


                $omy_result['battery_level'] = hexdec($omy_current_data[1]);


            } elseif ($omy_current_data[0] == "0d") {


                $omy_result['last_bclick'] = hexdec($omy_current_data[1]) + (hexdec($omy_current_data[2]) * 256);



            } elseif ($omy_current_data[0] == "11") {


                $omy_result['bclick_count'] = hexdec($omy_current_data[1]);


            } elseif ($omy_current_data[0] == "0f") {


                $omy_result['timestamp'] = hexdec("{$omy_current_data[1]}{$omy_current_data[2]}{$omy_current_data[3]}{$omy_current_data[4]}");


            } elseif ($omy_current_data[0] == "12") {


                $omy_result['humidity'] = hexdec($omy_current_data[1]);


            } elseif ($omy_current_data[0] == "13") {


                if ($omy_current_data[2] != 0) {

                    $omy_result['temperature'] = round(hexdec($omy_current_data[2]) + (hexdec($omy_current_data[1]) / 256), 1, PHP_ROUND_HALF_UP);

                }


            } elseif ($omy_current_data[0] == "16") {


                $omy_result['move_count'] = hexdec($omy_current_data[1]);
                $omy_result['last_move'] = hexdec($omy_current_data[2]) + (hexdec($omy_current_data[3]) * 256);


            }


        }


        unset($omy_payload);
        unset($omy_payload_length);

        $omy_temp["payload"] = $omy_result;  



        // $omy_cache = new Redis();
        // $omy_cache->connect(getenv('REDIS_HOST'), getenv('REDIS_PORT'));


        // $omy_tracker = $omy_cache->hGetAll("DEVICE:TRACKER:DATA:vivo:{$omy_temp['mac_address_device']}");

        // $omy_cache->close();


        // customLogger("", "BLE :: AP : {$omy_temp['mac_address_ap']} | DEVICE  : {$omy_temp['mac_address_device']} [ {$omy_tracker['name']} ] | TIME : {$omy_temp['time']}");


        // if(empty($omy_tracker)) return;
        // if($omy_tracker == false) return;
        


        // customLogger("", "BLE :: AP : {$omy_temp['mac_address_ap']} | DEVICE  : {$omy_temp['mac_address_device']} [ {$omy_tracker['name']} ] | PUSH MQTT");

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


            // if($omy_temp['mac_address_device'] == "F35BBFC5B670") {
                
            //     if(!empty($omy_temp['payload'])) {

            //         var_dump(json_encode($omy_temp["payload"]));

            //         echo "\n================\n";
            //     }

            // }

            $omy_mqtt->publish('DEVICE/AP/DATA', json_encode($omy_temp), 0, false);
            $omy_mqtt->close();


        }else {

            customLogger(LOG_PATH_GENERAL, "[huawei-ble] Fail to connect MQTT");

        }

        ############################### END #############################
        ##
        ## MQTT FUNCTION --  ALL AGENT WILL PUSH TO MQTT
        ##
        #################################################################


        unset($omy_data, $omy_temp, $omy_mqtt);

    });

    return;



});


$omy_server->start();

