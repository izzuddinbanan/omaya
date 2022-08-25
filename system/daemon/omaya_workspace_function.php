<?php 



require_once dirname(__FILE__, 2) . "/tools/phpmailer/Exception.php";
require_once dirname(__FILE__, 2) . "/tools/phpmailer/PHPMailer.php";
require_once dirname(__FILE__, 2) . "/tools/phpmailer/SMTP.php";


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
function checkTime($omy_start, $omy_end){


    // if empty date time, then always return true

	return true;
    if (!empty($omy_start) && !empty($omy_end)) {

        $omy_end_ori = $omy_end;

        // if not empty, the check if time now in between those time

        if (strlen($omy_start) == 5) {

            $omy_start = DateTime::createFromFormat("H:i", $omy_start);

        } else $omy_start = new DateTime($omy_start, new DateTimeZone("UTC"));

        return $omy_start = $omy_start->getTimestamp();


        if (strlen($omy_end) == 5) {

            $omy_end = DateTime::createFromFormat("H:i", $omy_end);

        } else $omy_end = new DateTime($omy_end, new DateTimeZone("UTC"));

        $omy_end = $omy_end->getTimestamp();

        if($omy_end < $omy_start) {

            $omy_end = DateTime::createFromFormat("H:i", $omy_end_ori);
            $omy_end = $omy_end->modify('+1 day')->getTimestamp();

        }   

        $omy_now = new DateTime('now', new DateTimeZone("UTC"));
        $omy_now = $omy_now->getTimestamp();


        if (($omy_now > $omy_start) && ($omy_now < $omy_end)) {

            return true;

        } else return false;


    } 

    return true;


}



function send_notification($omy_db, $omy_cache, $omy_rule, $omy_data)
{

    // get the list of admin to send the data


    if (empty($omy_rule['send_to_role'])) return;

	$omy_rule['send_to_role'] = explode(",", $omy_rule['send_to_role']);
	$where_role = "'" . implode("','", $omy_rule['send_to_role']) . "'";


    $omy_admins = $omy_db->query("SELECT SQL_CACHE username,email FROM omaya_users WHERE tenant_id = '{$omy_data['tenant_id']}' AND role IN ({$where_role})");

    $location = $omy_db->query("SELECT SQL_CACHE * FROM omaya_locations WHERE location_uid = '{$omy_data['device_controller']['location_uid']}' LIMIT 1")[0];

    $venue = $omy_db->query("SELECT SQL_CACHE * FROM omaya_venues WHERE venue_uid = '{$omy_data['device_controller']['venue_uid']}' LIMIT 1")[0];

    $zone = $omy_db->query("SELECT SQL_CACHE * FROM omaya_zones WHERE zone_uid = '{$omy_data['device_controller']['zone_uid']}' LIMIT 1")[0];

    if(empty($zone)) {
    	$zone['name'] = "nozone";
    } 



    $center_x   = $venue['image_width'] / 2;
    $center_y   = $venue['image_height'] / 2;


    if(($omy_data['device_controller']['position_x'] < $center_x && $omy_data['device_controller']['position_y'] < $center_y)){ //top left marker

        $tracker['position_x'] = $omy_data['device_controller']['position_x'] + rand(9,99);
        $tracker['position_y'] = $omy_data['device_controller']['position_y'] + rand(9,99);
    }

    elseif($omy_data['device_controller']['position_x'] > $center_x && $omy_data['device_controller']['position_y'] < $center_y) { //top right marker

        $tracker['position_x'] = $omy_data['device_controller']['position_x'] - rand(9,99);
        $tracker['position_y'] = $omy_data['device_controller']['position_y'] + rand(9,99);
    }
    elseif($omy_data['device_controller']['position_x'] < $center_x && $omy_data['device_controller']['position_y'] > $center_y) { //bottom left marker
        $tracker['position_x'] = $omy_data['device_controller']['position_x'] + rand(9,99);
        $tracker['position_y'] = $omy_data['device_controller']['position_y'] - rand(9,99);
    }
    elseif($omy_data['device_controller']['position_x'] > $center_x && $omy_data['device_controller']['position_y'] > $center_y) { //bottom right marker
        $tracker['position_x'] = $omy_data['device_controller']['position_x'] - rand(9,99);
        $tracker['position_y'] = $omy_data['device_controller']['position_y'] - rand(9,99);
    }

    unset($center_x, $center_y);



    do {

        $uid = randomStringUid();

    } while ($omy_db->query("SELECT id FROM omaya_notifications where tenant_id = '{$omy_data['tenant_id']}' AND notification_uid = '{$uid}' LIMIT 1")[0]);


    ## INSERT DB
    $temp = [];
    $temp["tenant_id"]          = $omy_data['tenant_id'];
    $temp["notification_uid"]   = $uid;
    $temp["rssi"]           = "-" . (int) ($omy_data['rssi']);
    $temp["rule_uid"]         	= $omy_rule['rule_uid'];
    $temp["location_uid"]       = $location['location_uid'];
    $temp["venue_uid"]          = $venue['venue_uid'];
    $temp["zone_uid"]           = $zone['zone_uid'] ?? NULL;
    $temp["device_controller_uid"] = $omy_data['device_controller']["device_uid"];
    $temp["device_tracker_uid"] 	= $omy_data['device_tracker']["device_uid"];
    $temp["entity_uid"]         = !empty($omy_data['entity']) ? $omy_data['entity']['entity_uid'] : NULL;
    $temp["position_x"]         = $tracker['position_x'];
    $temp["position_y"]         = $tracker['position_y'];
    $temp["trigger_value"]      = $omy_data['trigger_value'];
    $temp["trigger_at"]      	= date("Y-m-d H:i:s", $omy_data['updated_at']);
    $temp["created_at"]         = date("Y-m-d H:i:s");
    $temp["updated_at"]         = date("Y-m-d H:i:s");

    $omy_db->query(sql_insert($omy_db, "omaya_notifications", $temp));
    unset($temp);


    ## INSERT DB
    $temp = [];
    $temp["tenant_id"]          = $omy_data['tenant_id'];
    $temp["notification_uid"]   = $uid;
	$temp["created_by"]         = "1";
    $temp["updated_by"]         = "1";
    $temp["created_at"]         = date("Y-m-d H:i:s");
    $temp["updated_at"]         = date("Y-m-d H:i:s");
    $omy_db->query(sql_insert($omy_db, "omaya_notification_histories", $temp));
    unset($temp);


    if(empty($omy_rule['action'])) return;

    $omy_rule['action'] = explode(",", $omy_rule['action']);


    foreach ($omy_rule['action'] as $action) {

        $email_list = [];
    	foreach ($omy_rule['send_to_role'] as $send_to_role) {
	    	
	    	if($action == "alert") {

                $temp = [];
                $temp["tenant_id"]      = $omy_data['tenant_id'];
                $temp["rssi"]           = "-" . (int) ($omy_data['rssi']);
                $temp["notification_uid"]   = $uid;
                $temp["rule"]           = $omy_rule;;
                $temp["location"]       = $location;
                $temp["venue"]          = $venue;
                $temp["zone"]           = $zone;
                $temp["device_controller"] = $omy_data['device_controller'];
                $temp["device_tracker"]    = $omy_data['device_tracker'];
                $temp["entity"]            = !empty($omy_data['entity']) ? $omy_data['entity'] : NULL;
                $temp["position_x"]        = $tracker['position_x'];
                $temp["position_y"]        = $tracker['position_y'];
                $temp["trigger_value"]     = $omy_data['trigger_value'];
                $temp["trigger_at"]        = date("Y-m-d H:i:s", $omy_data['updated_at']);


    			$omy_cache->set("OMAYA:ALERT:{$omy_data['tenant_id']}:{$uid}:{$send_to_role}", serialize($temp), 604800); // set expire to 7 day

    			unset($temp);


	    	}else if($action == "email"){

                    
                $temp_email = $omy_db->query("SELECT email, username FROM omaya_users where tenant_id = '{$omy_data['tenant_id']}' AND role = '{$send_to_role}'");


                $email_list = array_merge($email_list, $temp_email);


            }


    	}

        if($action == "email") {


            if(count($email_list) > 0){




                $kiw_temp['host'] = 'smtp.mailtrap.io';
                $kiw_temp['port'] = '2525';
                $kiw_temp['auth'] = 'tls';
                $kiw_temp['user'] = 'ecb73b691965e4';
                // $kiw_temp['password'] = '49610f75776245';
                $kiw_temp['password'] = '';
                $kiw_temp['from_email'] = 'no-reply@gmail.com';
                $kiw_temp['from_name'] = 'adib';

                $omy_email = new PHPMailer(false);

                $omy_email->Timeout = 10;
                $omy_email->SMTPDebug = 0;

                $omy_email->Host = trim($kiw_temp['host']);
                $omy_email->Port = trim($kiw_temp['port']);


                if (!empty($kiw_temp['auth']) && $kiw_temp['auth'] != "none") {

                    $omy_email->SMTPSecure = trim($kiw_temp['auth']);

                }


                if (!empty($kiw_temp['user']) && !empty($kiw_temp['password'])) {

                    $omy_email->SMTPAuth = true;

                    $omy_email->Username = trim($kiw_temp['user']);
                    $omy_email->Password = trim($kiw_temp['password']);

                }


                $omy_email->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );


                try {


                    $omy_email->isSMTP();
                    $omy_email->setFrom(trim("no-reply@omaya.synchroweb.com"), "alert-omaya");
                    $omy_email->ClearAllRecipients();


                    foreach ($email_list as $email_send) {
                        $omy_email->addAddress($email_send["email"], $email_send["username"]);
                    }

                    $omy_email->addReplyTo(trim($kiw_temp['from_email']));
                    $omy_email->isHTML(true);

                    $omy_email->Subject = "Omaya Notification";
                    $omy_email->Body = "Dear User, Alert have trigger on Omaya Service. Please check at Omaya portal.";

                    // send the email to smtp server

                    $omy_email->send();

                    if ($omy_email->ErrorInfo == "") {

                        $kiw_status = "succeed";

                    } else $kiw_status = $omy_email->ErrorInfo;


                } catch (Exception $e){


                    $omy_email->Body = "";
                    $omy_email->clearAllRecipients();
                    $omy_email->clearAttachments();
                    $omy_email->smtpClose();

                    $kiw_status = $e->getMessage();


                }

            }




        }




    }


    unset($omy_alert);


}


function randomStringUid() {

    $length = 10;

    if($length > 19) $length = 20;

    $char = '0123456789abcdefghijklmnopqrstuvwxyz';


    $char_len   = strlen($char);
    $rand_str   = '';

    for ($i = 0; $i < $length; $i++) {

        $rand_str .= $char[random_int(0, $char_len - 1)];

    }


    return $rand_str;


}