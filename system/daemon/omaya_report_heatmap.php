<?php

if(!isset($argv[1])) die("No current ID for process. Please insert ID.\n");

### CUSTOM LOAD .env FILE  #####
require_once dirname(__FILE__, 2)."/tools/DotEnv.php";
(new DotEnv(dirname(__FILE__, 3)."/.env"))->load();


// Base function
require_once dirname(__FILE__, 2) . "/tools/sql.helper.php";
require_once dirname(__FILE__, 2) . "/tools/constant.php";
require_once dirname(__FILE__, 3) . "/app/Supports/helper.php";


define("LOG_PATH", "/daemon/omaya_report_heatmap");


$omy_cache = new Redis();
$omy_cache->connect(getenv('REDIS_HOST'), getenv('REDIS_PORT'));

$omy_db = mysqlConnection();
// IF FAIL CONNECTION
if ($omy_db->connect_errno) {

    echo "MYSQL fail to connect. \n";
    customLogger(LOG_PATH, "MYSQL fail to connect.");
    return;
}


$omy_data = $omy_db->query("SELECT * FROM omaya_raw_massages WHERE id = '{$argv[1]}' LIMIT 1");

$omy_data = $omy_data->fetch_all(MYSQLI_ASSOC)[0];


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


$omy_db->query("UPDATE omaya_raw_massages SET heatmap_report_status = 'pending', is_heatmap_count = '{$is_heatmap_count}' WHERE id = '{$argv[1]}'");


$omy_cache->close();
$omy_db->close();
unset($omy_db, $omy_cache);


