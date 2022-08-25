<?php
###################### START ################################
# Checking service. only 1 service can run at a time. if not, duplicate data
#############################################################
$omy_service = exec('ps aux | grep omaya_prereport_processor.php | grep -v grep | wc -l');

if($omy_service > 1) {
        
    echo "Fail to start service. Service already running. \nPlease stop the existing service first. \n\n***** NOTE *****\n Run 'systemctl stop omaya_prereport_processor' OR \n Kill the process id --check pid by 'ps aux | grep omaya_prereport_processor.php'\n****************\n\n";
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


$omy_swoole['port'] 	 		= 8999;
$omy_swoole['daemonize'] 		= 1;
$omy_swoole['pid_file'] 		= "/run/omaya-prereport-processor.pid";
$omy_swoole["debug_data"]		= false; //true or false . Enable this for debug


$omy_server = new swoole_server("127.0.0.1", 8999, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);

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
            # CHECKING TENANT LICENSE 
            ############################################
            $omy_license = $omy_cache->hGetAll("TENANT:LICENSE:{$omy_data['tenant_id']}");

            if(empty($omy_license)) {

                $omy_cache->set("OMAYA:JOB:TENANT-LICENSE:{$omy_data["tenant_id"]}", $omy_data["tenant_id"], 30);
                customLogger("agent", "AP : {$omy_data['mac_address_ap']} | DEV : {$omy_data['mac_address_device']} = Get TENANT-LICENSE from license file");

                return;
                
            }

            if ($omy_license['dummy'] == "1" || $omy_license['dummy'] == true) {

                customLogger($omy_data['tenant_id'], "AP : {$omy_data['mac_address_ap']} | DEV : {$omy_data['mac_address_device']} = License not valid.");
                return;
            
            }
            ########################END###############################

            
            ###################### START ###############
            # CHECK IF OMAYA TYPE IS WORKSPACE
            ############################################    

            // Mac address device is only for testing
            if($omy_license['type'] != "crowd" || $omy_data['mac_address_device'] == "F35BBFC5B670") {


                $omy_conn_workspace = new swoole_client(SWOOLE_TCP);
                $omy_conn_workspace->connect('127.0.0.1', 8998, 1);



                if($omy_conn_workspace->isconnected()){

                    $omy_conn_workspace->send(json_encode($omy_data));
                    $omy_conn_workspace->close();

                }
                

            }else {


                ###################### START ###############
                # CHECK IF DEVICE IS REGISTERED
                ############################################
                $omy_tracker = $omy_cache->hGetAll("DEVICE:TRACKER:DATA:{$omy_data['tenant_id']}:{$omy_data['mac_address_device']}");

                if (empty($omy_tracker)) {


                    $omy_exist = $omy_db->query("SELECT * FROM omaya_device_counts WHERE mac_address_device = '{$omy_data['mac_address_device']}'  AND tenant_id = '{$omy_data['tenant_id']}'")[0];



                    if(empty($omy_exist)) {

                        $omy_store_device = [];
                        $omy_store_device["tenant_id"]          = $omy_data['tenant_id'];
                        $omy_store_device["mac_address_device"] = $omy_data['mac_address_device'];
                        $omy_store_device["created_at"]         = "NOW()";
                        $omy_store_device["updated_at"]         = "NOW()";

                        $omy_db->query(sql_insert($omy_db, "omaya_device_counts", $omy_store_device));

                    }else {


                        $temp_date    = date("Y-m-d");
                        $temp_compare = date("Y-m-d", strtotime($omy_exist['updated_at']));

                        if($temp_date != $temp_compare) {

                            if((int) ($omy_exist['total_count'] + 1) > 3) {


                                // check if already blacklist
                                $omy_list = $omy_db->query("SELECT id FROM omaya_device_lists WHERE mac_address_device = '{$omy_data['mac_address_device']}'  AND tenant_id = '{$omy_data['tenant_id']}'")[0];



                                if(empty($omy_list)) {

                                    $omy_store_device = [];
                                    $omy_store_device["tenant_id"]          = $omy_data['tenant_id'];
                                    $omy_store_device["mac_address_device"] = $omy_data['mac_address_device'];
                                    $omy_store_device["is_blacklist"]       = true;
                                    $omy_store_device["created_by"]         = "service";
                                    $omy_store_device["updated_by"]         = "service";
                                    $omy_store_device["created_at"]         = "NOW()";
                                    $omy_store_device["updated_at"]         = "NOW()";

                                    $omy_db->query(sql_insert($omy_db, "omaya_device_lists", $omy_store_device));
                                    unset($omy_store_device);


                                }
                                $omy_cache->set("OMAYA:BLACKLIST:{$omy_data['mac_address_device']}", true);



                            }



                            $omy_store_device = [];
                            $omy_store_device["total_count"]    = (int) $omy_exist['total_count'] + 1;
                            $omy_store_device["updated_at"]     = "NOW()";

                            $omy_db->query(sql_update($omy_db, "omaya_device_counts", $omy_store_device, "mac_address_device = '{$omy_data['mac_address_device']}' AND tenant_id = '{$omy_data['tenant_id']}'"));

                        }


                    }
                }



                unset($omy_exist, $omy_store_device);



            }






            $omy_exist = $omy_db->query("SELECT * FROM omaya_raw_massages WHERE mac_address_device = '{$omy_data['mac_address_device']}'  AND tenant_id = '{$omy_data['tenant_id']}' AND last_seen_at  > (NOW() - INTERVAL  900 SECOND) ORDER BY id DESC LIMIT 1")[0];

            if (empty($omy_exist)) {


                $omy_db->query(sql_insert($omy_db, "omaya_raw_massages", insertRawData($omy_data)));


            }else {


                ###################### START ###############
                # GET DATA TENANT
                ############################################
                $omy_tenant = $omy_cache->hGetAll("TENANT:DATA:{$omy_exist['tenant_id']}");

                if (empty($omy_tenant)) {

                    $omy_tenant = $omy_db->query("SELECT * FROM omaya_clouds WHERE tenant_id = '{$omy_exist['tenant_id']}' LIMIT 1")[0];

                    if (empty($omy_tenant)) $omy_tenant = array("dummy" => true);
                    
                    $omy_cache->hMSet("TENANT:DATA:{$omy_exist['tenant_id']}", $omy_tenant);

                }

                if($omy_tenant["dummy"] == true) return;

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


                // If not same location venue zone or new day, create new data
                if(($omy_exist["location_uid"] != $omy_ap['location_uid']) || ($omy_exist["venue_uid"] != $omy_ap['venue_uid']) || ($omy_exist["zone_uid"] != $omy_ap['zone_uid']) || getDateLocal($omy_exist['first_seen_at'], $omy_tenant['timezone'], 'd') !=  getDateLocal($omy_exist['last_seen_at'], $omy_tenant['timezone'], 'd')) {

                    

                    $is_cross_visit = true;
                    if($omy_tenant['is_filter_dwell_time']) {


                        if((strtotime($omy_exist['last_seen_at']) - strtotime($omy_exist['first_seen_at'])) < $omy_tenant['remove_dwell_time']) {

                            $is_cross_visit = false;
                            $omy_db->query("DELETE FROM omaya_raw_massages WHERE id = '{$omy_exist["id"]}'");

                        }


                    }else {

                        if($omy_exist['last_seen_at'] == $omy_exist['first_seen_at']) {
                           
                            $is_cross_visit = false;
                            $omy_db->query("DELETE FROM omaya_raw_massages WHERE id = '{$omy_exist["id"]}'");


                        }

                    }

                    


                    ## process cross visit
                    if($is_cross_visit == true) {


                        $cross_visit['cross_visit_report_status'] = 'pending';
                        $cross_visit['previous_mac_address_ap']   = $omy_exist['mac_address_ap'];
                    }


                    $omy_db->query(sql_insert($omy_db, "omaya_raw_massages", array_merge(insertRawData($omy_data), $cross_visit ?? [])));


                }else {



                    $omy_temp = [];
                    $omy_temp['last_seen_at']   = $omy_data['raw_data']['seen_at'];

                    if(abs($omy_data['rssi']) < abs($omy_exist['rssi'])) {

                        $omy_temp['rssi'] = "-" . (int) $omy_data['rssi']; 
                    }

                    $omy_temp['updated_at'] = "NOW()";


                    $omy_dwell_sec = (strtotime($omy_temp['last_seen_at']) - strtotime($omy_exist['first_seen_at']));


                    $process_now = true;
                    if(($omy_tenant['is_filter_dwell_time'] == true) && ($omy_dwell_sec < $omy_tenant['remove_dwell_time'])) $process_now = false;


                    if($process_now == true) {


                        if(empty($omy_exist["dwell_report_table"])) 
                            $omy_temp["dwell_report_table"] = "omaya_report_dwell_" . date("Ym", strtotime($omy_exist['first_seen_at']));

                        if(empty($omy_exist["report_time"]))
                            $omy_temp["report_time"] = date('Y-m-d H:00:00', strtotime($omy_exist['first_seen_at']));




                        if($omy_exist["dwell_report_status"] == NULL) {

                            $omy_exist["dwell_report_status"] = $omy_temp["dwell_report_status"]  = "pending";

                        }


                        if($omy_exist["dwell_last"] == NULL) $omy_temp["dwell_last"] = 0;



                        if($omy_exist["dwell_report_status"] == "pending") {

                            $omy_temp["dwell_now"] = (int) ($omy_dwell_sec / 60);

                            $omy_temp["dwell_group_now"] = dwellGroup($omy_temp["dwell_now"]);


                        }else if($omy_exist["dwell_report_status"] == "completed") {

                            $omy_temp["dwell_report_status"] = "pending";
                            $omy_temp["dwell_last"]          = $omy_exist["dwell_now"];
                            $omy_temp["dwell_now"]           = (int) ($omy_dwell_sec / 60);


                            $omy_temp["dwell_group_now"] = dwellGroup($omy_temp["dwell_now"]);
                            $omy_temp['dwell_group_last']= $omy_exist['dwell_group_now'];


                        }






                        ### FOR GENERAL REPORT

                        if($omy_exist["general_report_status"] == NULL || $omy_exist["general_report_status"] == "completed") {

                            if($omy_exist["general_report_status"] == "completed")
                                $omy_temp["general_last"] = $omy_exist["general_now"];

                            $omy_exist["general_report_status"] = "pending";
                            $omy_temp["general_report_status"]  = "pending";


                        }


                        if(empty($omy_exist["general_report_table"])) 
                            $omy_temp["general_report_table"] = "omaya_report_general_" . date("Ym", strtotime($omy_exist['first_seen_at']));




                        ## FOR REPORT DEVICE CONTROLLER

                        if(empty($omy_exist["device_controller_report_status"]))
                            $omy_temp["device_controller_report_status"] = "pending";
           

                    }

                    $omy_db->query(sql_update($omy_db, "omaya_raw_massages", $omy_temp, " id = '{$omy_exist['id']}'"));


                    // Update for general report
                    if($process_now == true) {


                        if($omy_exist["general_report_status"] == "pending" || ($omy_exist["heatmap_report_status"] == NULL || empty($omy_exist["heatmap_report_status"]) || $omy_exist["heatmap_report_status"] == "completed")) {


                            $omy_raw_massage = $omy_db->query("SELECT * FROM omaya_raw_massages WHERE id = '{$omy_exist['id']}' LIMIT 1")[0];


                            if($omy_exist["general_report_status"] == "pending") report_general($omy_db, $omy_cache, $omy_raw_massage);


                            if($omy_exist["heatmap_report_status"] == NULL || empty($omy_exist["heatmap_report_status"]) || $omy_exist["heatmap_report_status"] == "completed") report_heatmap($omy_db, $omy_cache, $omy_raw_massage);

                        }



                    }


                    unset($omy_dwell_sec, $process_now);


                }


            }


            $omy_db->close();
            $omy_cache->close();
            unset($omy_db, $omy_cache);



        }

        return;

    });
    

});



function insertRawData($omy_data) {

    $omy_temp = [];
    $omy_temp['tenant_id']          = $omy_data['tenant_id'];
    $omy_temp['mac_address_ap']     = $omy_data['mac_address_ap'];
    $omy_temp['mac_address_device'] = $omy_data['mac_address_device'];
    $omy_temp['device_vendor']      = $omy_data['raw_data']['device_vendor'];
    $omy_temp['rssi']               = "-" . (int) ($omy_data['rssi']);
    $omy_temp['rssi_type']          = $omy_data['raw_data']['rssi_type'];
    $omy_temp['first_seen_at']      = $omy_data['raw_data']['seen_at'];
    $omy_temp['last_seen_at']       = $omy_data['raw_data']['seen_at'];
    $omy_temp['venue_uid']          = $omy_data['raw_data']['venue_uid'];
    $omy_temp['location_uid']       = $omy_data['raw_data']['location_uid'];
    $omy_temp['zone_uid']           = $omy_data['raw_data']['zone_uid'];
    // $omy_temp['device_controller_report_status']           = "pending";
    // $omy_temp['device_controller_count_column']           = "packet_total";
    // $omy_temp['dwell_current']      = "dwell_15";
    // $omy_temp['dwell_next']         = "dwell_30";
    $omy_temp['created_at']         = "NOW()";
    $omy_temp['updated_at']         = "NOW()";
    $omy_temp['raw_data']           = json_encode($omy_data['raw_data']);

    $omy_temp["report_time"]        = date('Y-m-d H:00:00', strtotime($omy_temp['first_seen_at']));


    return $omy_temp;


}


function report_general($omy_db, $omy_cache, $omy_data){


    if((empty($omy_data["general_last"]) && empty($omy_data["general_now"])) || $omy_data["general_report_status"] ==  "pending"){


        $new_row = false;
        if(empty($omy_data["general_last"]) && empty($omy_data["general_now"])) $new_row = true;



        $report_date = date('Y-m-d', strtotime($omy_data['first_seen_at']));


        $query = "SELECT * FROM omaya_device_histories WHERE report_date = '{$report_date}' AND location_uid = '{$omy_data['location_uid']}' AND venue_uid = '{$omy_data['venue_uid']}' AND mac_address_device = '{$omy_data['mac_address_device']}' ". (empty($omy_data['zone_uid']) ? " " : " AND zone_uid = '{$omy_data['zone_uid']}'") ." LIMIT 1";
        $omy_unique = $omy_db->query($query)[0];
        unset($query);


        $found_data = true;
        if(empty($omy_unique)) {

            $temp["report_date"]        = $report_date;
            $temp["tenant_id"]          = $omy_data['tenant_id'];
            $temp["location_uid"]       = $omy_data['location_uid'];
            $temp["venue_uid"]          = $omy_data['venue_uid'];
            $temp["zone_uid"]           = $omy_data['zone_uid'];
            $temp["mac_address_device"] = $omy_data['mac_address_device'];
            $temp["created_at"]         = date("Y-m-d H:i:s");
            $temp["updated_at"]         = date("Y-m-d H:i:s");

            $omy_db->query(sql_insert($omy_db, "omaya_device_histories", $temp));
            unset($temp);
            $found_data = false;

        }
        unset($omy_unique);


        $temp["dwell_sec"] = strtotime($omy_data['last_seen_at']) - strtotime($omy_data['first_seen_at']);

        if($temp["dwell_sec"] < 60) $omy_temp["general_now"] = "passby";
        else $omy_temp["general_now"] = "visit";
        if($omy_data['is_engaged']) $omy_temp["general_now"] = "engaged";


        if($omy_temp["general_now"] != $omy_data['general_now']) {


            $unique_query = " ";
            if($new_row == true && $found_data == true) $unique_query = "  , general_return_device = '1' ";


            $general_last = $omy_data["general_last"];

            if(empty($omy_data['general_last'])) $general_last = 'none';

            // Update record
            $query = "UPDATE omaya_raw_massages SET general_last = '{$general_last}' , general_now = '{$omy_temp['general_now']}' {$unique_query} WHERE id = '{$omy_data['id']}'";

            $omy_db->query($query);
        }






    }

}




function report_heatmap($omy_db, $omy_cache, $omy_data){

    $report_date = date('Y-m-d H:00:00', strtotime($omy_data['last_seen_at']));


    // SET DATA IN REDIS FOR 70 MINUTES
    $ori_zone = $omy_data["zone_uid"];
    if(empty($omy_data["zone_uid"])) $omy_data["zone_uid"] = "nozone";

    $heatmap_hour = $omy_cache->get("OMAYA:REPORT:HEATMAP:{$omy_data['tenant_id']}:{$omy_data['location_uid']}:{$omy_data['venue_uid']}:{$omy_data['zone_uid']}:{$report_date}:{$omy_data['mac_address_device']}"); 

    $is_heatmap_count = 0;

    if($heatmap_hour == false) {


        // SET DATA IN REDIS FOR 70 MINUTES
        
        $omy_cache->set("OMAYA:REPORT:HEATMAP:{$omy_data['tenant_id']}:{$omy_data['location_uid']}:{$omy_data['venue_uid']}:{$omy_data['zone_uid']}:{$report_date}:{$omy_data['mac_address_device']}", "checked", 3900); 
        $is_heatmap_count = 1;
    }


    $omy_db->query("UPDATE omaya_raw_massages SET heatmap_report_status = 'pending', is_heatmap_count = '{$is_heatmap_count}' WHERE id = '{$omy_data['id']}'");


}


$omy_server->start();


