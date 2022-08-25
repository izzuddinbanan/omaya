<?php
###################### START ################################
# Checking service. only 1 service can run at a time. if not, duplicate data
#############################################################
$omy_service = exec('ps aux | grep omaya_workspace_service.php | grep -v grep | wc -l');

if($omy_service > 1) {
        
    echo "Fail to start service. Service already running. \nPlease stop the existing service first. \n\n***** NOTE *****\n Run 'systemctl stop omaya_workspace_service' OR \n Kill the process id --check pid by 'ps aux | grep omaya_workspace_service.php'\n****************\n\n";
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


require_once dirname(__FILE__, 1) . "/omaya_workspace_function.php";


$omy_swoole['port'] 	 		= 8998;
$omy_swoole['daemonize'] 		= 1;
$omy_swoole['pid_file'] 		= "/run/omaya-workspace-service.pid";
$omy_swoole["debug_data"]		= false; //true or false . Enable this for debug


$omy_server = new swoole_server("127.0.0.1", $omy_swoole['port'], SWOOLE_PROCESS, SWOOLE_SOCK_TCP);

$omy_config = ["worker_num" => 3, "max_conn" => 1024];


$omy_server->set(array(
    'worker_num'    => $omy_config["worker_num"] ?? 3,
    'daemonize'     => $omy_swoole['daemonize'],
    'max_conn'      => $omy_config["max_conn"] ?? 1024,
    'max_request'   => 0,
    'pid_file'      => $omy_swoole['pid_file'],
    'group'         => 'nginx',
    'user'          => 'nginx',
));


$omy_server->on('Receive', function ($omy_server, $omy_fd, $omy_from, $omy_data) {



    go(function () use ($omy_data) {

        $omy_data = json_decode(trim($omy_data), true);


        if (is_array($omy_data)){


            $omy_cache = new Redis();
            $omy_cache->connect(getenv('REDIS_HOST'), getenv('REDIS_PORT'));


            $omy_db = new Swoole\Coroutine\MySQL();
            $omy_db->connect([
                'host' => getenv('DB_HOST'),
                'port' => getenv('DB_PORT'),
                'user' => getenv('DB_USERNAME'),
                'password' => getenv('DB_PASSWORD'),
                'database' => getenv('DB_DATABASE'),
                // 'charset' => 'utf8mb4',
            ]);



            


            ###################### START ###############
            # GET DATA DEVICE TRACKER
            ############################################
            $omy_tracker = $omy_cache->hGetAll("DEVICE:TRACKER:DATA:{$omy_data['tenant_id']}:{$omy_data['mac_address_device']}");


            if($omy_tracker["dummy"] == true) return;

            if($omy_tracker["is_active"] == 0) return;


            ########################END###############################



            ###################### START ###############
            # GET DATA CONTROLLER
            ############################################
            $omy_ap = $omy_cache->hGetAll("DEVICE:AP:DATA:{$omy_data['mac_address_ap']}");


            if (empty($omy_ap)) {

                $omy_ap = $omy_db->query("SELECT * FROM omaya_device_controllers WHERE mac_address = '{$omy_data["mac_address_ap"]}' LIMIT 1")[0];


                if (empty($omy_ap)) $omy_ap = array("dummy" => true);

                else $omy_cache->hMSet("DEVICE:AP:DATA:{$omy_data['mac_address_ap']}", $omy_ap);

            }

            if($omy_ap["dummy"] == true) return;

            ########################END###############################




            ###################### START ###############
            # GET RULE DATA
            ############################################
            $omy_rules = $omy_cache->get("RULE:DATA:{$data['tenant_id']}");


            if (empty($omy_rules)) {

                $omy_rules = $omy_db->query("SELECT * FROM omaya_rules WHERE tenant_id = '{$omy_data["tenant_id"]}'");

                $omy_rules = serialize($omy_rules);
                $omy_cache->set("DEVICE:RULES:{$omy_data['tenant_id']}", $omy_rules);

            }    

            $omy_rules = unserialize($omy_rules);


            ########################END###############################


            $updated_time = time();




            customLogger($omy_data['tenant_id'], "AP : {$omy_ap['mac_address']} [ {$omy_ap['name']} ] | DEV : {$omy_tracker['mac_address']} [ {$omy_tracker['name']} ] | check action to be done", 'workspace');


            ###################### START ###############
            # TRACKER BUTTON CLICK
            ############################################

            if(isset($omy_data['raw_data']['payload']['last_bclick'])) {


                $omy_cache->hSet("WORKSPACE:TRACKER:STATUS:{$omy_data['tenant_id']}:{$omy_data['mac_address_device']}", "last_bclick", $updated_time - $omy_data['raw_data']['payload']['last_bclick']);


                $temp_time = date("Y-m-d H:i:s", ($updated_time - $omy_data['raw_data']['payload']['last_bclick']));

                $omy_cache->hSet("WORKSPACE:TRACKER:STATUS:{$omy_data['tenant_id']}:{$omy_data['mac_address_device']}", "last_bclick_at", $temp_time);

                unset($temp_time);

                $omy_check = [];
                $omy_check['rssi']               = $omy_data['rssi'];
                $omy_check['tenant_id']          = $omy_data['tenant_id'];
                $omy_check['last_bclick']        = $omy_data['raw_data']['payload']['last_bclick'];
                $omy_check['device_controller']  = $omy_ap;
                $omy_check['device_tracker']     = $omy_tracker;
                $omy_check['entity']             = [];

                if($omy_tracker['is_allocated'] != 0) {


            
                    $omy_entity = $omy_db->query("SELECT SQL_CACHE * FROM omaya_entities WHERE device_tracker_uid = '{$omy_tracker["device_uid"]}' LIMIT 1")[0];


                    if (empty($omy_entity)) $omy_entity = [];

                    $omy_check['entity'] = $omy_entity;


                }


                // if($omy_data['mac_address_device'] == "DC9B67529D3F")
                // var_dump($omy_check['last_bclick']);


                $omy_check['updated_at']         = $updated_time;

                if($omy_check['last_bclick'] <= 13) {

                    omayaRule($omy_db, $omy_cache, "button_click", $omy_check, $omy_rules);
                    

                    customLogger($omy_data['tenant_id'], "AP : {$omy_ap['mac_address']} [ {$omy_ap['name']} ] | DEV : {$omy_tracker['mac_address']} [ {$omy_tracker['name']} ] Action : Click ", 'workspace');

                }

                unset($omy_check);

            }
            ########################END###############################



            ###################### START ###############
            # TRACKER TEMPERATUR
            ############################################

            if(isset($omy_data['raw_data']['payload']['temperature'])) {

                $omy_cache->hSet("WORKSPACE:TRACKER:STATUS:{$omy_data['tenant_id']}:{$omy_data['mac_address_device']}", "temperature", $omy_data['raw_data']['payload']['temperature']);
                
                customLogger($omy_data['tenant_id'], "AP : {$omy_ap['mac_address']} [ {$omy_ap['name']} ] | DEV : {$omy_tracker['mac_address']} [ {$omy_tracker['name']} ] Action : temperature ", 'workspace');

            }
            ########################END###############################


            ###################### START ###############
            # TRACKER HUMIDITY
            ############################################

            if(isset($omy_data['raw_data']['payload']['humidity'])) {

                $omy_cache->hSet("WORKSPACE:TRACKER:STATUS:{$omy_data['tenant_id']}:{$omy_data['mac_address_device']}", "humidity", $omy_data['raw_data']['payload']['humidity']);
                

                customLogger($omy_data['tenant_id'], "AP : {$omy_ap['mac_address']} [ {$omy_ap['name']} ] | DEV : {$omy_tracker['mac_address']} [ {$omy_tracker['name']} ] Action : humidity ", 'workspace');

            }
            ########################END###############################

            ###################### START ###############
            # TRACKER HUMIDITY
            ############################################

            if(isset($omy_data['raw_data']['payload']['battery_level'])) {

                $omy_cache->hSet("WORKSPACE:TRACKER:STATUS:{$omy_data['tenant_id']}:{$omy_data['mac_address_device']}", "battery_level", $omy_data['raw_data']['payload']['battery_level']);
                

                customLogger($omy_data['tenant_id'], "AP : {$omy_ap['mac_address']} [ {$omy_ap['name']} ] | DEV : {$omy_tracker['mac_address']} [ {$omy_tracker['name']} ] Action : battery level ", 'workspace');
            }
            ########################END###############################


            


            $omy_cache->hSet("WORKSPACE:TRACKER:STATUS:{$omy_data['tenant_id']}:{$omy_data['mac_address_device']}", "location_uid", $omy_ap['location_uid']);
            $omy_cache->hSet("WORKSPACE:TRACKER:STATUS:{$omy_data['tenant_id']}:{$omy_data['mac_address_device']}", "venue_uid", $omy_ap['venue_uid']);
            $omy_cache->hSet("WORKSPACE:TRACKER:STATUS:{$omy_data['tenant_id']}:{$omy_data['mac_address_device']}", "zone_uid", $omy_ap['zone_uid']);








            $omy_db->close();
            $omy_cache->close();
            unset($omy_db, $omy_cache);



        }

        return;

    });
    

});



function omayaRule($omy_db, $omy_cache, $omy_event, $omy_data, $omy_rules) {


    foreach ($omy_rules as $omy_rule) {
        
        if($omy_rule['event'] == $omy_event){

            if (empty($omy_rule['action_every'])) $omy_rule['action_every'] = 30;


            if (checkTime($omy_rule['start_time_action'], $omy_rule['stop_time_action']) == true) {


                if ($omy_rule['event'] == "button_click") {


                    $omy_key = "TRACKER:BUTTON_CLICK:{$omy_data['tenant_id']}:{$omy_data['device_tracker']['mac_address']}";


                    if ($omy_cache->exists($omy_key) == false) {



                        // create a redis entry to make sure no duplicate
                        $omy_cache->set($omy_key, true,  $omy_rule['action_every']);

                        // get the action to do for this button click event.
                        send_notification($omy_db, $omy_cache, $omy_rule, $omy_data);

                        // return $omy_data['updated_at'];
                    }


                }           




            }


        }

    }





}





$omy_server->start();


