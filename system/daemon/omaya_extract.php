<?php

###################### START ################################
# Checking service. only 1 service can run at a time. if not, duplicate data
#############################################################
$omy_service = exec('ps aux | grep omaya_extract.php | grep -v grep | wc -l');

if($omy_service > 1) {
        
    die("Fail to start service. Service already running. \nPlease stop the existing service first. \n\n***** NOTE *****\n Run 'systemctl stop omaya_extract' OR \n Kill the process id --check pid by 'ps aux | grep omaya_extract.php'\n****************\n\n");
}
###################### END #################################

### CUSTOM LOAD .env FILE  #####
require_once dirname(__FILE__, 2)."/tools/DotEnv.php";
(new DotEnv(dirname(__FILE__, 3)."/.env"))->load();


// Base function
require_once dirname(__FILE__, 2) . "/tools/sql.helper.php";
require_once dirname(__FILE__, 2) . "/tools/constant.php";
require_once dirname(__FILE__, 3) . "/app/Supports/helper.php";




function run() {

    $omy_cache = new Redis();
    $omy_cache->connect(getenv('REDIS_HOST'), getenv('REDIS_PORT'));


    $omy_conf['datetime']   = date('Y-m-d H:i:s');
    $omy_conf['second']     = date('s');

    $omy_cache->set("OMAYA:DAEMON:PROCESS:LAST_RUN_AT", $omy_conf['datetime']);
    $omy_cache->set("OMAYA:DAEMON:PROCESS:LAST_RUN_SECOND", $omy_conf['second']);



    $omy_current_id = $omy_cache->get("RAWDATA:CURRENT_ID");

    if($omy_current_id == false) return;

    // Delete current id so record not use anymore
    $omy_cache->del("RAWDATA:CURRENT_ID");
    $omy_cache->close();
    
    shell_exec("nohup /usr/bin/php /var/www/omaya/system/daemon/omaya_extract_process.php {$omy_current_id} 1>/dev/null 2>&1 &");


}

// Every 10s, execute the run function
Swoole\Timer::tick(5000, "run");