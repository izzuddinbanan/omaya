<?php

if(!isset($argv[1])) die("No current ID for process. Please insert ID.\n");
### CUSTOM LOAD .env FILE  #####
require_once dirname(__FILE__, 2)."/tools/DotEnv.php";
(new DotEnv(dirname(__FILE__, 3)."/.env"))->load();


// Base function
require_once dirname(__FILE__, 2) . "/tools/sql.helper.php";
require_once dirname(__FILE__, 2) . "/tools/constant.php";
require_once dirname(__FILE__, 3) . "/app/Supports/helper.php";


define("LOG_PATH", "/daemon/omaya_report_general");


$omy_db = mysqlConnection();
// IF FAIL CONNECTION
if ($omy_db->connect_errno) {

    echo "MYSQL fail to connect. \n";
    customLogger(LOG_PATH, "MYSQL fail to connect.");
    return;
}


$omy_data = $omy_db->query("SELECT * FROM omaya_raw_massages WHERE id = '{$argv[1]}' LIMIT 1");

$omy_data = $omy_data->fetch_all(MYSQLI_ASSOC)[0];


if((empty($omy_data["general_last"]) && empty($omy_data["general_now"])) || $omy_data["general_report_status"] ==  "pending"){


	// $new_row = false;
	// if(empty($omy_data["general_last"]) && empty($omy_data["general_now"])) $new_row = true;



	// $report_date = date('Y-m-d', strtotime($omy_data['first_seen_at']));


	// $query = "SELECT * FROM omaya_device_histories WHERE report_date = '{$report_date}' AND location_uid = '{$omy_data['location_uid']}' AND venue_uid = '{$omy_data['venue_uid']}' AND mac_address_device = '{$omy_data['mac_address_device']}' ". (empty($omy_data['zone_uid']) ? " " : " AND zone_uid = '{$omy_data['zone_uid']}'") ." LIMIT 1";
	// $omy_unique = $omy_db->query($query);
	// unset($query);

	// $omy_unique = $omy_unique->fetch_all(MYSQLI_ASSOC)[0];


	// $found_data = true;
	// if(empty($omy_unique)) {

	// 	$temp["report_date"] 		= $report_date;
	// 	$temp["location_uid"] 		= $omy_data['location_uid'];
	// 	$temp["venue_uid"] 			= $omy_data['venue_uid'];
	// 	$temp["zone_uid"] 			= $omy_data['zone_uid'];
	// 	$temp["mac_address_device"] = $omy_data['mac_address_device'];
	// 	$temp["created_at"]			= date("Y-m-d H:i:s");
	// 	$temp["updated_at"]			= date("Y-m-d H:i:s");

 //        $omy_db->query(sql_insert($omy_db, "omaya_device_histories", $temp));
 //        unset($temp);
	// 	$found_data = false;

	// }
	// unset($omy_unique);


	// $temp["dwell_sec"] = strtotime($omy_data['last_seen_at']) - strtotime($omy_data['first_seen_at']);

	// if($temp["dwell_sec"] < 60) $omy_temp["general_now"] = "passby";
	// else $omy_temp["general_now"] = "visit";
	// if($omy_data['is_engaged']) $omy_temp["general_now"] = "engaged";


	// if($omy_temp["general_now"] != $omy_data['general_now']) {


	// 	$unique_query = " ";
	// 	if($new_row == true && $found_data == true) $unique_query = "  , general_return_device = '1' ";


	// 	$general_last = $omy_data["general_last"];

	// 	if(empty($omy_data['general_last'])) $general_last = 'none';

	// 	// Update record
 //    	// $query = "UPDATE omaya_raw_massages SET general_last = '{$general_last}' , general_now = '{$omy_temp['general_now']}' {$unique_query} WHERE id = '{$argv[1]}'";


 //    	// $omy_db->query($query);
	// }






}


$omy_db->close();
unset($omy_db, $omy_cache);
