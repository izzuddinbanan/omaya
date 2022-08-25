<?php

namespace App\Console\Commands;

use App\Models\OmayaSystemService;
use Illuminate\Console\Command;

class OmayaService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'omaya:service {--reset=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checking all omaya service status.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $omy_time['start'] = date('Y-m-d H:i:s');
        $omy_cache = redisCache();


        // Argument to reset file
        $is_reset = $this->option('reset');


        if($is_reset == true || $is_reset == "true") $kiw_old_service_status = [];
        else {

            $kiw_old_service_status = @file_get_contents("/var/www/omaya/public/storage/omaya-service.json");

            $kiw_old_service_status = json_decode($kiw_old_service_status, true);

        }

        $kiw_service_status["last_checked"] = date("Y-m-d H:i:s");


        $services = OmayaSystemService::get();



        foreach ($services as $service) {

            if($service->is_enable) {

                if($service->service_name == "mariadb") {

                    if(config('database.default') == "mysql" && (config('database.connections.mysql.host') == "localhost" || config('database.connections.mysql.host') == "127.0.0.1") ) {

                        $kiw_temp = system("systemctl status mariadb | grep 'Active:' | awk -F ' ' '{print $2}'", $kiw_error);
                        $status_linux = system("systemctl status mariadb | grep 'Active:'", $status_error);

                        $status_linux = explode(";",  $status_linux);
                        $status_linux = $status_linux[1];


                        if (trim($kiw_temp) == "active" && $kiw_error == 0) {

                            $status = "active";

                        } else {

                            $status = "down";

                            shell_exec('systemctl restart mariadb > /dev/null 2>/dev/null &');
                        }

                        unset($kiw_temp);



                    }else {

                        $status = "unknown";
                        $message = "MariaDB is not running on same server";

                    }


                    $kiw_service_status["services"]["mariadb"]["name"]          = $service->name;
                    $kiw_service_status["services"]["mariadb"]["service_name"]  = $service->service_name;
                    if($status == "active") $kiw_service_status["services"]["mariadb"]["last_active"]   = date("Y-m-d H:i:s");
                    else $kiw_service_status["services"]["mariadb"]["last_active"] = $kiw_old_service_status["services"]["mariadb"]["last_active"] ?? "";
                    $kiw_service_status["services"]["mariadb"]["message"]       = $message ?? "";
                    $kiw_service_status["services"]["mariadb"]["image"]         = $service->images;
                    $kiw_service_status["services"]["mariadb"]["status"]        = $status;
                    $kiw_service_status["services"]["mariadb"]["status_linux"]  = $status_linux;
                    $kiw_service_status["services"]["mariadb"]["styles"]        = $service->image_styles;


                }else {



                     $kiw_temp = system("systemctl status {$service->service_name} | grep 'Active:' | awk -F ' ' '{print $2}'", $kiw_error);

                    $status_linux = system("systemctl status {$service->service_name} | grep 'Active:'", $status_error);

                    $status_linux = explode(";", $status_linux);
                    $status_linux = $status_linux[1] ?? "-";


                    if (trim($kiw_temp) == "active" && $kiw_error == 0) {

                        $status = "active";

                    } else {

                        $status = "down";

                        shell_exec('systemctl restart '. $service->service_name .' > /dev/null 2>/dev/null &');
                    }




                    $kiw_service_status["services"][$service->service_name]["name"] = $service->name;
                    $kiw_service_status["services"][$service->service_name]["service_name"] = $service->service_name;
                    if($status == "active") $kiw_service_status["services"][$service->service_name]["last_active"]   = date("Y-m-d H:i:s");
                    else $kiw_service_status["services"][$service->service_name]["last_active"] = $kiw_old_service_status["services"][$service->service_name]["last_active"] ?? "";
                    $kiw_service_status["services"][$service->service_name]["message"]       = $message ?? "";
                    $kiw_service_status["services"][$service->service_name]["image"]         = $service->images;
                    $kiw_service_status["services"][$service->service_name]["status"]        = $status;
                    $kiw_service_status["services"][$service->service_name]["status_linux"]  = $status_linux;
                    $kiw_service_status["services"][$service->service_name]["styles"]        = $service->image_styles;


                }


            }else {

                if($service->group == "add-on") shell_exec('systemctl stop '. $service->service_name .' > /dev/null 2>/dev/null &');

            }


        }

        

        @file_put_contents("/var/www/omaya/public/storage/omaya-service.json", json_encode($kiw_service_status));

        unset($status, $kiw_service_status, $kiw_temp, $message, $omy_image, $kiw_error, $service);
        
        // SET PERMISSION AGAIN
        system("sudo chown -R nginx:nginx /var/www/omaya/storage");
        system("sudo chmod -R 755 /var/www/omaya/storage/*");
        system("sudo chown -R nginx:nginx /var/www/omaya/bootstrap");
        // system("systemctl restart omaya_service");



        $this->info('Omaya Service scheduler successfully run.');


        $omy_time['end'] = date('Y-m-d H:i:s');
        $omy_cache->hMSet("OMAYA:SCHEDULER:OMAYA:SERVICE:RUN_AT", $omy_time);
        $omy_cache->close();

    }
}
