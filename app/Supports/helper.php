<?php


require_once dirname(__FILE__, 3) . "/system/tools/phpmailer/Exception.php";
require_once dirname(__FILE__, 3) . "/system/tools/phpmailer/PHPMailer.php";
require_once dirname(__FILE__, 3) . "/system/tools/phpmailer/SMTP.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


if(!function_exists('sendMail')){
    function sendMail($omy_smtp, $is_debug = false){


        $omy_temp['host']       = $omy_smtp["host"];
        $omy_temp['port']       = $omy_smtp["port"];
        $omy_temp['auth']       = $omy_smtp["auth"];
        $omy_temp['user']       = $omy_smtp["username"];
        $omy_temp['password']   = $omy_smtp["password"];
        $omy_temp['from_email'] = $omy_smtp["from_email"];
        $omy_temp['from_name']  = $omy_smtp["from_name"];
        $omy_temp['to_email']   = $omy_smtp["to_email"];
        $omy_temp['subject']    = $omy_smtp["subject"];
        $omy_temp['body']       = $omy_smtp["body"];



        $omy_email = new PHPMailer(false);

        $omy_email->Timeout = 10;
        $omy_email->SMTPDebug = $is_debug ? 1 : 0;
        // 1 = errors and messages
        // 2 = messages only

        $omy_email->Host = $omy_temp['host'];
        $omy_email->Port = $omy_temp['port'];


        if (!empty($omy_temp['auth']) && $omy_temp['auth'] != "none") {

            $omy_email->SMTPSecure = $omy_temp['auth'];

        }


        if (!empty($omy_temp['user']) && !empty($omy_temp['password'])) {

            $omy_email->SMTPAuth = true;

            $omy_email->Username = $omy_temp['user'];
            $omy_email->Password = $omy_temp['password'];

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
            $omy_email->setFrom(($omy_temp["from_email"] ?? "no-reply@omaya.synchroweb.com"), ($omy_temp['from_name'] ?? "Omaya"));
            $omy_email->ClearAllRecipients();


            foreach ($omy_temp["to_email"] as $email_send) {
                $omy_email->addAddress($email_send["email"], $email_send["name"]);
            }

            unset($email_send);


            $omy_email->addReplyTo(($omy_temp["from_email"] ?? "no-reply@omaya.synchroweb.com"));
            $omy_email->isHTML(true);

            $omy_email->Subject = $omy_temp["subject"];
            $omy_email->Body    = $omy_temp["body"];

            // send the email to smtp server

            $omy_email->send();

            if ($omy_email->ErrorInfo == "") {

                $omy_status = "succeed";

            } else $omy_status = $omy_email->ErrorInfo;


        } catch (Exception $e){


            $omy_email->Body = "";
            $omy_email->clearAllRecipients();
            $omy_email->clearAttachments();
            $omy_email->smtpClose();

            $omy_status = $e->getMessage();


        }


        return $omy_status;


    }

}


if(!function_exists('getDateLocal')){
    function getDateLocal($date, $timezone = "Asia/Kuala_Lumpur", $format = "Y-m-d H:i:s") {


        $date = new \DateTime(str_replace("/", "-", $date), new \DateTimeZone("UTC"));
        $date->setTimeZone(new \DateTimeZone($timezone));

        return $date->format($format);

      
    }       
}




if(!function_exists('getListCloud')){
    function getListCloud(){


        if(empty(session('list_tenant'))) {

            $tenants = \App\Models\OmayaCloud::select('tenant_id')->where('tenant_id', '<>', session('tenant_id'))->orderBy('tenant_id')->get();
            \Session::put('list_tenant'  , $tenants);

        }

        return session('list_tenant');


    }
}

if(!function_exists('respondAjax')){
    function respondAjax($status = "error", $message = "", $data = [], $http_code = 200){

        // $status = $status == "success" ? "success" : "error";
        
        return \Response::json(['status' => $status, "message" => $message, "data" => $data]);


    }
}

if(!function_exists('getMonthlyTable')){
function getMonthlyTable($omy_start, $omy_end, $omy_timezone, $omy_table_name)
    {


        $omy_table = [];

        $omy_start = new \DateTime($omy_start, new \DateTimeZone($omy_timezone));
        
        // $omy_end = date("Y-m-d H:i:s", strtotime($omy_end . " +1 Day -1 Seconds"));
        $omy_end = date("Y-m-d H:i:s", strtotime($omy_end));
        $omy_end = new \DateTime($omy_end, new \DateTimeZone($omy_timezone));

        $omy_interval = $omy_start->diff($omy_end)->format("%a");

        
        if($omy_interval == 0) $omy_interval = 1;

        foreach (range($omy_interval, 0) as $omy_range) {


            $omy_current_date = date("Ym", strtotime($omy_end->format("Y-m-d H:i:s") . "-{$omy_range} Day"));

            $omy_current_date = $omy_table_name . "_" . $omy_current_date;


            if (!in_array($omy_current_date, $omy_table)) {

                $omy_table[] = $omy_current_date;

            }


        }

        return $omy_table;

    }
}


if(!function_exists('getEachDateBetweenDate')){
    function getEachDateBetweenDate($startDate, $endDate, $format = 'Y-m-d')
    {
        $rangArray = [];
            
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
             
        for ($currentDate = $startDate; $currentDate <= $endDate; 
                                        $currentDate += (86400)) {
                                                
            $date = date($format, $currentDate);
            $rangArray[] = $date;
        }
  
        return $rangArray;
    }
}


if(!function_exists('getDateStart')){
    function getDateStart($format = "Y-m-d H:i:s") {

        $time =  \App\Models\OmayaCloud::select('created_at')->where('tenant_id', session('tenant_id'))->first();

        return date($format, strtotime($time->created_at));
        return $time->created_at;
    }
}



if(!function_exists('reportDateStart')){
    function reportDateStart($start_date = "", $interval = 30)
    {

        $timezone = session('timezone');

        if (!empty($start_date)) {

            $date = new \DateTime(str_replace("/", "-", $start_date), new \DateTimeZone($timezone));
            $date->setTimeZone(new \DateTimeZone("UTC"));

        } else {

            $date = new \DateTime("now", new \DateTimeZone("UTC"));
            $date->setTimeZone(new \DateTimeZone($timezone));
            $date = new \DateTime($date->format("Y-m-d 00:00:00"), new \DateTimeZone($timezone));
            $date->setTimeZone(new \DateTimeZone("UTC"));

            if ($interval == 30) $interval = cal_days_in_month(CAL_GREGORIAN, date("m", strtotime("-1 month")), date("Y", strtotime("-1 month")));

            $interval = "P{$interval}D";
            $date->sub(new \DateInterval($interval));
        }

        return $date->format("Y-m-d H:i:s");
    }
}



if(!function_exists('reportDateEnd')){
    function reportDateEnd($end_date = "", $interval = 1)
    {

        $timezone = session('timezone');

        if (!empty($end_date)) {

            $date = new \DateTime(str_replace("/", "-", $end_date), new \DateTimeZone($timezone));
            $date->setTimeZone(new \DateTimeZone("UTC"));
        } else {

            $date = new \DateTime("now", new \DateTimeZone("UTC"));
            $date->setTimeZone(new \DateTimeZone($timezone));
            $date = new \DateTime($date->format("Y-m-d 00:00:00"), new \DateTimeZone($timezone));
            $date->setTimeZone(new \DateTimeZone("UTC"));

            $interval = "P{$interval}D";
            $date->sub(new \DateInterval($interval));
        }

        return date("Y-m-d H:i:s", strtotime($date->format("Y-m-d H:i:s") . " +1 day -1 second"));
    }
}



if(!function_exists('dwellGroup')){
    function dwellGroup($current_dwell) {
        

        if($current_dwell < 15)  {

            $dwell_group = "dwell_15";

        }
        else if($current_dwell  >= 15 && $current_dwell  < 30) {
            $dwell_group = "dwell_30";

        }
        else if($current_dwell  >= 30 && $current_dwell  < 60) {
            $dwell_group = "dwell_60";

        }
        else if($current_dwell >= 60 && $current_dwell  < 120) {
            $dwell_group = "dwell_120";

        }
        else if($current_dwell  >= 120 && $current_dwell  < 240) {
            $dwell_group = "dwell_240";

        }
        else if($current_dwell  >= 240 && $current_dwell  < 480) {
            $dwell_group = "dwell_480";

        }
        else 
            $dwell_group = "dwell_more";


        return $dwell_group;
        

    }
}

if(!function_exists('generateTempLicense')){
    function generateTempLicense() {


        $omy_client['vendor']       = "Default";
        $omy_client['product']      = "omaya";
        $omy_client['client_name']  = "default";
        $omy_client['device_limit'] = 10;
        $omy_client['expire_on']    = strtotime(date('Y-m-d', strtotime(date("Y-m-d") . ' + 15 days')));
        $omy_client['multi-tenant'] = false;
        $omy_client['type']         = "crowd";
        $omy_client['triangulation']= false;
        $omy_client['your_name']    = "Syncroweb-dev";
        $omy_client['generate_on']  = time();
        $omy_client['trial']        = true;
        $omy_license                = json_encode($omy_client);
        $omy_license                = openssl_encrypt($omy_license, "AES-256-CBC", "e1gOtk*9Ox_R", 0, "7vO*STBUdm_7tU4i");


        return base64_encode($omy_license);

      
    }
}


if(!function_exists('pullOuiStandard')){

    function pullOuiStandard($url = "https://raw.githubusercontent.com/vincenzogianfelice/OUI/master/oui.txt") {
        // https://raw.githubusercontent.com/vincenzogianfelice/OUI/master/oui.txt
        // https://gitlab.com/wireshark/wireshark/-/raw/master/manuf
        $omy_pull_data = file_get_contents($url);
        $omy_arr       = explode("\n", $omy_pull_data);
        $omy_prefix    = array();
        
        if(!empty($omy_pull_data)){
            
            foreach ($omy_arr as $line) {
                
                if($line){
                    
                    $line_data = explode("\t",$line);
                    if(!empty($line_data) && $line_data[0][0]!="#")  {
                        
                        $omy_prefix[]= $line_data[0];

                        
                        if($oui = \App\Models\OmayaOuiStandard::where('mac_address', $line_data[0])->first()){
                            $oui->touch();
                        }
                        else{

                            \App\Models\OmayaOuiStandard::create([
                                'mac_address'   => $line_data[0],
                                'vendor'        => $line_data[1],
                            ]);
                           
                        }


                    }

                }
                
            }

            unset($omy_arr);
            unset($oui);
            
            $unused_oui = \App\Models\OmayaOuiStandard::whereDate('updated_at', '!=', date('Y-m-d'));
            if(count($unused_oui->get()) > 0) $unused_oui->delete();
            
            unset($unused_oui);
            return true;

        }

        return false;


    }
}



if(!function_exists('distance')){

    function distance($h1, $w1, $h2, $w2) {

        /**
         * REFERENCE = https://stackoverflow.com/questions/28474358/php-calc-distance-between-two-points-result-in-pixels
         * 
         */

        //Pythagoras Theorem.
        $dh = $h1 - $h2;
        $dw = $w1 - $w2;

        
        return sqrt($dh*$dh + $dw*$dw);

        // Manhattan Distance 
        // return abs($h1 - $h2) + abs($w1 - $w2);
    }

}



if(!function_exists('customLogger')) {
    function customLogger($path, $message, $file_name = "log"){

        if (strlen($message) > 0){

            $base_path = dirname(__FILE__, 3) . "/storage/logs/";
  
            foreach (explode('/', $path) as $value) {

                $base_path = $base_path . "/" . $value;
                // check if directory existed, if not then create
                if(file_exists($base_path) == false) mkdir($base_path, 0755, true);

            }

            file_put_contents("{$base_path}/{$file_name}-" . date("Ymd-H") . ".log", date("Y-m-d H:i:s :: ") . $message . "\n", FILE_APPEND);

        }

    }
}

if(!function_exists('userLogs')) {
    function userLogs($action){
        $user = \Auth::user();

        \App\Models\OmayaWebuserlog::create([
            'tenant_id' => session('tenant_id'),
            'username'  => $user->username,
            'action'    => $action,
        ]);
    }
}

if(!function_exists('redisCache')) {

    function redisCache() {
        $omy_cache = new \Redis();
        $omy_cache->connect(config('database.redis.default.host'), config('database.redis.default.port'));
        return $omy_cache;
    }

}

if(!function_exists('badgeCustom')) {

    function badgeCustom($type, $text) {

        return '<div class="badge badge-'. $type .'">
                <span>'. $text .'</span>
            </div>';
    }
}

if(!function_exists('buttonCustom')) {

    function buttonCustom($type, $text) {

        return '<button class="btn btn-sm btn-'. $type .'">'. $text .'</button>';
    }
}



if (!function_exists('suspendCustomButton')) {

    function suspendCustomButton($target_url, $button_name, $className='', $icon ){
        return '<a class="dropdown-item '.$className.'" href="'. $target_url .'">
                    <i class="'.$icon.' alert-link  text-warning"></i>
                    <span>'.$button_name.'</span>
                </a>';

    }
}

if (!function_exists('editCustomButton')) {

    function editCustomButton($target_url){

        return '<a class="dropdown-item" href="'. $target_url .'">
                    <i class="fa fa-pencil alert-link  text-primary"></i>
                    <span>Edit</span>
                </a>';

    }
}


if (!function_exists('deleteCustomButton')) {

    function deleteCustomButton($target_url){
        return '<a class="dropdown-item ajaxDeleteButton" href="'. $target_url .'">
                    <i  class=" fa fa-trash alert-link  text-danger"></i>
                    <span>Delete</span>
                </a>';
    }
}


if (!function_exists('actionCustomButton')) {

    function actionCustomButton($html){
        return  '<div class="dropdown">
                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0" data-bs-toggle="dropdown">
                <span class="alert-link text-primary">More  <i class="fa fa-angle-double-down alert-link"></i></span>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                '. $html .'
                </div>
            </div>';

    }
}


if (!function_exists('getCloudLicense')) {

    function getCloudLicense() {

        $path = "storage/cloud.license";

        $omy["result"]          = true;
        if(is_file($path)){

            // TO FIX ERROR WHEN RUN IN SSL
            $options = [
                "ssl"   =>  [
                    "verify_peer"       =>false,
                    "verify_peer_name"  =>false,
                ],
            ];  


            $omy['multi-license'] = file_get_contents(public_path($path), false, stream_context_create($options));

            $omy['multi-license'] = sync_license_decode($omy['multi-license']);


            if ($omy['multi-license']['product'] == "omaya" && $omy['multi-license']['multi-tenant'] == "true") {


                if ($omy['multi-license']['multi-tenant']['type'] !== "trial" || $omy['multi-license']['multi-tenant']['expiry'] > time()) {


                    $omy['multi-tenant'] = true;


                } 
                else $omy['multi-tenant'] = false;


            } 
            else {

                $omy['multi-tenant']    = false;
                $omy["result"]          = false;

            }


        } 
        else {

            $omy["result"]      = false;
            $omy['error-msg']   = "ERROR: Please check your license.";
        
        } 

        return $omy;

        
    }

}



if (!function_exists('checkLicense')) {

    function checkLicense($license) {


        $license = sync_license_decode($license);

        if($license){

            if(time() > $license["expire_on"]){

                $omy['error-msg'] = "ERROR: Your license already expired. Please contact our Sales representative.";

            }
            elseif($license["product"] != "omaya"){

                $omy['error-msg'] = "ERROR: Wrong license provided. [ License is not meant for Omaya - 001 ].";

            }
            elseif(!in_array($license["type"], ['crowd', 'workspace', 'vision'])){
                
                $omy['error-msg'] = "ERROR: Wrong license provided. [ License is not meant for Omaya - 002].";

            }
            else{

                $license["result"] = true;

                return $license;
            }


        }else {

            $omy['error-msg'] = "ERROR: Wrong license provided. [ License is not meant for Omaya ].";

        }




        $omy["result"]       = false;


        return $omy;
    }
}

if (!function_exists('getTenantLicense')) {

    function getTenantLicense($tenant_id, $is_laravel = true, $path = "") {


        if($is_laravel) {

            $path = "storage/tenants/{$tenant_id}/tenant.license";
        
        }
        
        if(is_file($path)){
            
            // TO FIX ERROR WHEN RUN IN SSL
            $options = [
                "ssl"   =>  [
                    "verify_peer"       =>false,
                    "verify_peer_name"  =>false,
                ],
            ];  
            
            
            if($is_laravel)
            $omy['tenant-license'] = file_get_contents(public_path($path), false, stream_context_create($options));
            
            else 
            $omy['tenant-license'] = file_get_contents($path);


            $license = sync_license_decode($omy['tenant-license']);

            
            unset($omy['tenant-license']);
            
            if($license){
                
                if(time() > $license["expire_on"]){

                    $omy['error-msg'] = "ERROR: Your license already expired. Please contact our Sales representative.";

                }
                elseif($license["product"] != "omaya"){

                    $omy['error-msg'] = "ERROR: Wrong license provided. [ License is not meant for Omaya ].";

                }
                elseif(!in_array($license["type"], ["crowd", "workspace"])){

                    $omy['error-msg'] = "ERROR: Wrong license provided. [ License is not meant for Omaya Type [crowd, workspace] ].";

                }
                else{

                    $license["result"] = true;
                    return $license;

                }


            }else {
                
                $omy['error-msg'] = "ERROR: Wrong license provided.";

            }


        }else{

            $omy['error-msg'] = "ERROR: Please check your license.";

        }

        $omy["multi-tenant"] = false;
        $omy["result"]       = false;

        return $omy;
    }
}




function applClasses()
{
    $data = config('custom.custom');
    $layoutClasses = [
        'theme' => $data['theme'],
        'sidebarCollapsed' => $data['sidebarCollapsed'],
        'navbarColor' => $data['navbarColor'],
        'navbarType' => $data['navbarType'],
        'footerType' => $data['footerType'],
        'sidebarClass' => 'menu-expanded',
        'bodyClass' => $data['bodyClass']
    ];

            
    
    //Theme
    if($layoutClasses['theme'] == 'dark')
        $layoutClasses['theme'] = "dark-layout";
    elseif($layoutClasses['theme'] == 'semi-dark')
        $layoutClasses['theme'] = "semi-dark-layout";
    else
        $layoutClasses['theme'] = "";

    //navbar
    switch($layoutClasses['navbarType']){
        case "static":
            $layoutClasses['navbarType'] = "navbar-static";
            break;
        case "sticky":
            $layoutClasses['navbarType'] = "navbar-sticky";
            break;
        case "hidden":
            $layoutClasses['navbarType'] = "navbar-hidden";
            break;
        default:
            $layoutClasses['navbarType'] = "navbar-floating";
    }

    // sidebar Collapsed
    if($layoutClasses['sidebarCollapsed'] == 'true')
        $layoutClasses['sidebarClass'] = "menu-collapsed";

    //footer
    switch($layoutClasses['footerType']){
        case "sticky":
            $layoutClasses['footerType'] = "fixed-footer";
            break;
        case "hidden":
            $layoutClasses['footerType'] = "footer-hidden";
            break;
        default:
            $layoutClasses['footerType'] = "footer-static";
    }

    return $layoutClasses;
}


if (!function_exists('getProfilePhoto')) {

    function getProfilePhoto(){

        $user = \Auth::user();

        if($user->photo){
            
            return url('storage/tenants/' . session('tenant_id') . '/user/thumbnails/' . removeStringAfterCharacters($user->photo) .'.jpg');
        }

        return url('images/no-photo.png');
    }
}




if (!function_exists('getNoPhotoAvailable')) {

    function getNoPhotoAvailable(){
        return url('images/no-photo.png');
    }
}


if (!function_exists('randomStringId')) {
    
    function randomStringId() {

        $length = config('general.random_uid_length') ?? 8;

        if($length > 19) $length = 20;

        $char = '0123456789abcdefghijklmnopqrstuvwxyz';


        $char_len   = strlen($char);
        $rand_str   = '';

        for ($i = 0; $i < $length; $i++) {

            $rand_str .= $char[random_int(0, $char_len - 1)];

        }


        return $rand_str;


    }
}
    

function sync_license_decode($license_string = null) {

    if (!empty($license_string)) {


        $license_string = json_decode(openssl_decrypt(base64_decode($license_string), "AES-256-CBC", "e1gOtk*9Ox_R", 0, "7vO*STBUdm_7tU4i"), true);

        if (is_array($license_string)) return $license_string;
        else return false;


    } else return false;
}


function sync_logger($message = "", $tenant_id = "")
{


    $tenant_id = preg_replace('/[^A-Za-z0-9 _ .-]/', '', $tenant_id);


    // check if path available. if not then create

    if (file_exists(dirname(__FILE__, 4) . "/logs/{$tenant_id}/") == false) mkdir(dirname(__FILE__, 4) . "/logs/{$tenant_id}/", 0755, true);


    // if empty tenant, set to general

    if (empty($tenant_id)) $tenant_id = "general";


    // set the filename

    $filename = "kiwire-system-{$tenant_id}-" . date("Ymd-H") . ".log";


    if ($_SESSION['access_level'] == "superuser") {

        $message = "[ SU ] {$message}";
    }


    // push message to log file

    file_put_contents(dirname(__FILE__, 4) . "/logs/{$tenant_id}/{$filename}", date("Y-m-d H:i:s") . " : {$message}" . "\n", FILE_APPEND);
}

if (!function_exists('converTimeToLocal')) {
    function converTimeToLocal($time, $timezone, $format = "d M Y h:ia"){

        return (new Carbon\Carbon($time))->timezone($timezone)->format($format);
         
    }
}


function syncToLocalTime($time = null, $zone = "Asia/Kuala_Lumpur")
{

    try {

        $x = str_replace("/", "-", $time);
        $x = date('Y-m-d H:i:s', strtotime($x));

        $x = new DateTime($x, new DateTimeZone("UTC"));
        $x->setTimezone(new DateTimeZone($zone));

        return $x->format('Y-m-d H:i:s');
    } catch (Exception $e) {

        return false;
    }
}


if (!function_exists('removeStringAfterCharacters')) {

    function removeStringAfterCharacters($string, $after = ".")
    {
        return substr($string, 0, strpos($string, $after));

    }
}



function sync_toutctime($time = null, $zone = "Asia/Kuala_Lumpur")
{

    try {

        $x = str_replace("/", "-", $time);
        $x = date('Y-m-d H:i:s', strtotime($x));

        $x = new DateTime($x, new DateTimeZone($zone));
        $x->setTimezone(new DateTimeZone("UTC"));

        return $x->format('Y-m-d H:i:s');
    } catch (Exception $e) {

        return false;
    }
}


function sync_accessible($page_module = null, $module_list = null)
{

    if (!empty($page_module) && !empty($module_list)) {

        if (in_array($page_module, $module_list)) return true;
        return false;
    }
}


function sync_encrypt($raw_string)
{

    return base64_encode(openssl_encrypt($raw_string, "AES-256-CBC", SYNC_ENC_KEY, 0, SYNC_ENC_IV));
}


function sync_decrypt($raw_string)
{

    return openssl_decrypt(base64_decode($raw_string), "AES-256-CBC", SYNC_ENC_KEY, 0, SYNC_ENC_IV);
}


// warning: do not use this sync_brand_encrypt function, use sync_encrypt instead
// this function just for internal use, not suppose to be exposed to public

function sync_brand_encrypt($raw_string)
{

    return base64_encode(openssl_encrypt($raw_string, "AES-256-CBC", "sync_*lifKx/", 0, "L*qF_n8QZslc5qVO"));
}

// warning: do not use this sync_brand_decrypt function, use sync_decrypt instead
// this function just for internal use, not suppose to be exposed to public

function sync_brand_decrypt($raw_string)
{

    return openssl_decrypt(base64_decode($raw_string), "AES-256-CBC", "sync_*lifKx/", 0, "L*qF_n8QZslc5qVO");
}


function sync_hash_message($raw_string)
{

    return hash_hmac("SHA256", $raw_string, "synchro*hash_mac");
}


function saveFile($path, $file, $old_file = "")
{



    if (!empty($file['name'])) {


        if (file_exists($path) == false) {


            mkdir($path, 0755, true);
        }


        $omy_file_name = time() . rand(10, 99) . "." . pathinfo($file['name'], PATHINFO_EXTENSION);


        move_uploaded_file($file['tmp_name'], "{$path}/{$omy_file_name}");


        if ($old_file) {


            if (file_exists("{$path}/{$old_file}")) {


                unlink("{$path}/{$old_file}");
            }
        }


        return $omy_file_name;
    }

    return null;
}


function convertImageToBase64($image, $include_header = true)
{

    $type = pathinfo($image, PATHINFO_EXTENSION);
    $data = file_get_contents($image);


    if ($include_header)
        return 'data:image/' . $type . ';base64,' . base64_encode($data);


    return base64_encode($data);
}

function checkValidation($omy_validation)
{


    $omy_validation->validate();


    if ($omy_validation->fails()) {


        $omy_errors = $omy_validation->errors();
        $omy_errors = $omy_errors->firstOfAll();

        echo json_encode(array("status" => "error", "message" => $omy_errors, "data" => null));
        exit;
    }
}

function formatDateDiff($start, $end=null, $enablehour=false) {
    if(!($start instanceof DateTime)) {
        $start = new DateTime($start);
    }

    if($end === null) {
        $end = new DateTime();
    }

    if(!($end instanceof DateTime)) {
        $end = new DateTime($start);
    }

    $interval = $end->diff($start);
    $doPlural = function($nb,$str){return $nb>1?$str.'s':$str;}; // adds plurals

    $format = array();
    if($interval->y !== 0) {
        $format[] = "%y ".$doPlural($interval->y, "year");
    }
    if($interval->m !== 0) {
        $format[] = "%m ".$doPlural($interval->m, "month");
    }
    if($interval->d !== 0) {
        $format[] = "%d ".$doPlural($interval->d, "day");
    }
    if($enablehour===true){
        if($interval->h !== 0) {
            $format[] = "%h ".$doPlural($interval->h, "hour");
        }
        if($interval->i !== 0) {
            $format[] = "%i ".$doPlural($interval->i, "minute");
        }
        if($interval->s !== 0) {
            if(!count($format)) {
                return "less than a minute ago";
            } else {
                $format[] = "%s ".$doPlural($interval->s, "second");
            }
        }
    }
    // We use the two biggest parts
    if(count($format) > 1) {
        $format = array_shift($format)." and ".array_shift($format);
    } else {
        $format = array_pop($format);
    }

    // Prepend 'since ' or whatever you like
    return $interval->format("%r".$format);
}

if (!function_exists('able_to')) {
    /**
     * @param $module_name || name from table module
     * @param $permission || value from config permission_actions => actions || create | update | delete | view
     */
    function able_to($group_name ,$module_name = null, $permission = null)
    {
        if (auth()->user()->role != 'superuser') {

            if(!in_array($group_name, session('access_group'))){
                return false;
            }

            if(!empty($module_name)) {

                if(!in_array("{$group_name}:{$module_name}", session('access_list'))){
                    return false;
                }
            }

            if (!is_null($permission)) {

                if(auth()->user()->permission == 'r' && $permission == 'rw') {

                    return false;
                }

            }
        }

        return true;
    }
}


if (!function_exists('mysqlConnection')) {

    function mysqlConnection() {

        $omy_db = new mysqli(getenv('DB_HOST'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_DATABASE'), getenv('DB_PORT'));

        return $omy_db;
    }
}


if (!function_exists('bin8dec')) {
    function bin8dec($bin) {

        $num = bindec($bin);

        if($num > 0xFF) { return false; }

        if($num >= 0x80) return -(($num ^ 0xFF)+1);
        else return $num;

    }
}





if (!function_exists('allowPush')) {
    function allowPush($omy_db, $omy_cache, $mac_address_ap, $mac_address_device, $rssi_type = 'wifi') {

   
        ###################### START ###############
        # CHECKING DEVICE CONTROLLER AP TO GET TENANT DATA
        ############################################

        // remove all char in mac addres
        $mac_address_ap = str_replace([':', '-', ' ', '.'], "", $mac_address_ap);


        $omy_ap = $omy_cache->hGetAll("DEVICE:AP:DATA:{$mac_address_ap}");


        if (empty($omy_ap)) {

            $omy_ap = $omy_db->query("SELECT SQL_CACHE * FROM omaya_device_controllers WHERE mac_address = '{$mac_address_ap}' LIMIT 1");

            $omy_ap = $omy_ap->fetch_all(MYSQLI_ASSOC)[0];


            if (empty($omy_ap)) $omy_ap = array("dummy" => true);
            
            $omy_cache->hMSet("DEVICE:AP:DATA:{$mac_address_ap}", $omy_ap);

        }


        if ($omy_ap['dummy'] == "1" || $omy_ap['dummy'] == true) return false;

        if($omy_ap['is_active'] == false) return false;



        ###################### START ###############
        # CHECKING TENANT LICENSE 
        ############################################
        $omy_license = $omy_cache->hGetAll("TENANT:LICENSE:{$omy_ap['tenant_id']}");
        if(empty($omy_license)) {

            $omy_license = getTenantLicense($license, false, "/var/www/omaya/public/storage/tenants/{$omy_ap['tenant_id']}/tenant.license");
            

            if(empty($omy_license) || $omy_license["result"] == false) $omy_license = array("dummy" => true);

            $omy_cache->hMSet("TENANT:LICENSE:{$omy_ap['tenant_id']}", $omy_license);
        }


        if ($omy_license['dummy'] == "1" || $omy_license['dummy'] == true) {

            customLogger("{$omy_ap['tenant_id']}", "AP : {$mac_address_ap} | DEV : {$mac_address_device} = License not valid.");

            return false;

        }




        ###################### START ###############
        # GET DATA TENANT
        ############################################

        $omy_tenant = $omy_cache->hGetAll("TENANT:DATA:{$omy_ap['tenant_id']}");

        if (empty($omy_tenant)) {

            $omy_tenant = $omy_db->query("SELECT SQL_CACHE * FROM omaya_clouds WHERE tenant_id = '{$omy_ap["tenant_id"]}' LIMIT 1");

            $omy_tenant = $omy_tenant->fetch_all(MYSQLI_ASSOC)[0];


            if (empty($omy_tenant)) $omy_tenant = array("dummy" => true);
            
            $omy_cache->hMSet("TENANT:DATA:{$omy_ap['tenant_id']}", $omy_tenant);

        }


        if ($omy_tenant['dummy'] == "1" || $omy_tenant['dummy'] == true) {

            customLogger("{$omy_ap['tenant_id']}", "AP : {$mac_address_ap} | DEV : {$mac_address_device} = Tenant not found.");
            return false;
        }



        if ($omy_tenant['is_active'] == "0") return false;

        ########################END###############################



        ###################### START ###############
        # CHECK LIMIT DEVICE AP
        ############################################

        $omy_count = $omy_cache->hGetAll("AP:COUNT:{$omy_ap['tenant_id']}");

        if (empty($omy_count)) {

            $omy_count = $omy_db->query("SELECT SQL_CACHE count(*) AS ocount FROM omaya_device_controllers WHERE tenant_id = '{$omy_ap["tenant_id"]}'");

            $omy_count = $omy_count->fetch_all(MYSQLI_ASSOC)[0];


            if (empty($omy_count)) $omy_count = array("dummy" => true);
            
            $omy_cache->hMSet("AP:COUNT:{$omy_ap['tenant_id']}", $omy_count);

        }


        if ($omy_count['dummy'] == true) return false;


        if ($omy_count['ocount'] > $omy_license['device_limit']) {

            customLogger("{$omy_ap['tenant_id']}", "AP : {$mac_address_ap} | DEV : {$mac_address_device} = Device [AP] reach max limit. Please use other license or delete device [AP].");
            return false;

        }

        ########################END###############################


        $omy_tracker = $omy_db->query("SELECT SQL_CACHE * FROM omaya_device_trackers WHERE mac_address = '{$mac_address_device}' LIMIT 1");

        $omy_tracker = $omy_tracker->fetch_all(MYSQLI_ASSOC)[0];

        $is_registered = false;
        if(!empty($omy_tracker)) $is_registered = true;

        if($is_registered == false) {


            ###################### START ###############
            # FILTER RANDOM MAC ADDRESS
            ############################################
            if($omy_tenant['is_filter_mac_random']) {

                // Refer https://www.mist.com/get-to-know-mac-address-randomization-in-2020/#:~:text=Fortunately%20it%20is%20easy%20to,it%20is%20a%20randomized%20address.
                if(in_array($mac_address_device[1], ['2', '6', 'A', 'E'])) return false;


            }
            ###########################END############################


            ###################### START ###############
            # FILTER OUI MAC ADDRESS
            ############################################
            $omy_filter_mac = filterOuiMacAddress($omy_db, $omy_cache, $mac_address_device);
            if($omy_tenant['is_filter_oui'] && $rssi_type == 'wifi') {


                if($omy_filter_mac['status'] == false) return false;

            }

            ###########################END############################
        }


        customLogger("{$omy_ap['tenant_id']}", "AP : {$mac_address_ap} | DEV : {$mac_address_device} = Success checking and filtering");


        return $omy_tenant;

    }
}



if (!function_exists('filterOuiMacAddress')) {
    function filterOuiMacAddress($omy_cache, $omy_mac) {

        $omy_db     = mysqlConnection();

        $omy_mac = substr($omy_mac, 0 , 6);
        // $omy_oui = $omy_cache->hGetAll("SYSTEM:OUI:MAC:{$omy_mac}");

        if (empty($omy_oui)) {


            $omy_oui = $omy_db->query("SELECT SQL_CACHE vendor FROM omaya_oui_standards WHERE mac_address = '{$omy_mac}' LIMIT 1");
            $omy_oui = $omy_oui->fetch_all(MYSQLI_ASSOC)[0];

            $omy_oui = (empty($omy_oui)) ? ["status" => false, "vendor" => NULL] : ["status" => true, "vendor" => strtolower(trim($omy_oui['vendor']))];

            // $omy_cache->hMSet("SYSTEM:OUI:MAC:{$omy_mac}", $omy_oui);

        }
        $omy_db->close();
        unset($omy_db);

        return $omy_oui;



    }
}

