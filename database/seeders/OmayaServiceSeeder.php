<?php

namespace Database\Seeders;

use App\Models\OmayaSystemService;
use Illuminate\Database\Seeder;

class OmayaServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        \DB::table('omaya_system_services')->truncate();

        collect([

            [   'group'         => 'general',
                'name'          => 'MariaDB', 
                'service_name'  => 'mariadb', 
                'images'        => "images/mariadb.png",
                'image_styles'  => 'border-radius: 0px !important;max-height: 30px !important;', 
                'remarks'       => 'Database management system for Omaya App. Only available if DB is on local server.', 
                'is_enable'     => true,
            ],
            [   'group'         => 'general',
                'name'          => 'Nginx', 
                'service_name'  => 'nginx', 
                'images'        => 'images/nginx.png',
                'image_styles'  => 'border-radius: 0px !important;max-height: 44px !important;', 
                'remarks'       => 'Web server for Omaya App.', 
                'is_enable'     => true,
            ],
            [   'group'         => 'general',
                'name'          => 'redis', 
                'service_name'  => 'redis', 
                'images'        => 'images/redis.png',
                'image_styles'  => 'border-radius: 0px !important;max-height: 25px !important;', 
                'remarks'       => 'We use to Cache data for improve the App performance.', 
                'is_enable'     => true,
            ],
            [   'group'         => 'general',
                'name'          => 'php-fpm', 
                'service_name'  => 'php-fpm', 
                'images'        => 'images/php.png', 
                'image_styles'  => 'border-radius: 0px !important;max-height: 43px !important;', 
                'remarks'  => 'Backend process', 

                'is_enable'     => true,
                
            ],
            [   'group'         => 'general',
                'name'          => 'mosquitto [MQTT Broker]', 
                'service_name'  => 'mosquitto', 
                'images'        => 'images/mqtt.png',
                'image_styles'  => 'border-radius: 0px !important;max-height: 35px !important;', 
                'remarks'       => 'Process millions of MQTT messages per second, guarantee millisecond latency in messaging.', 
                'is_enable'     => true,
            ],

            [   'group'         => 'general',
                'name'          => 'Omaya Service', 
                'service_name'  => 'omaya_service', 
                'images'        => 'images/logo.png', 
                'image_styles'  => 'border-radius: 0px !important;max-height: 24px !important;', 
                'remarks'       => 'Main process for Omaya App processing all data and push to MQTT.', 
                'is_enable'     => true,
            ],
            [   'group'         => 'general',
                'name'          => 'Omaya Workspace Service', 
                'service_name'  => 'omaya_workspace_service', 
                'images'        => 'images/logo.png', 
                'image_styles'  => 'border-radius: 0px !important;max-height: 24px !important;', 
                'remarks'       => 'Main process for Omaya Workspace processing all data.', 
                'is_enable'     => true,
            ],

            [   'group'         => 'general',
                'name'          => 'Omaya Extract', 
                'service_name'  => 'omaya_extract', 
                'images'        => 'images/logo.png', 
                'image_styles'  => 'border-radius: 0px !important;max-height: 24px !important;', 
                'remarks'       => 'Load and extract all data from MQTT to separate the data into reporting', 
                'is_enable'     => true,
            ],
            [   'group'         => 'general',
                'name'          => 'Omaya Pre Report Processor', 
                'service_name'  => 'omaya_prereport_processor', 
                'images'        => 'images/logo.png', 
                'image_styles'  => 'border-radius: 0px !important;max-height: 24px !important;', 
                'remarks'       => 'Omaya pre report is service for pre processing data before data insert into reporting table.', 
                'is_enable'     => true,
            ],
            [   'group'         => 'general',
                'name'          => 'Omaya Job', 
                'service_name'  => 'omaya_job', 
                'images'        => 'images/logo.png', 
                'image_styles'  => 'border-radius: 0px !important;max-height: 24px !important;', 
                'remarks'       => 'Omaya job is a queue service that running in background', 
                'is_enable'     => true,
            ],
            [   'group'         => 'add-on',
                'name'          => 'Omaya Agent : Huawei [Wifi]', 
                'service_name'  => 'omaya_agent_huawei_wifi', 
                'images'        => 'images/logo.png', 
                'image_styles'  => 'border-radius: 0px !important;max-height: 24px !important;', 
                'remarks'       => 'Omaya Agent for <b>Huawei [ Wifi ]</b> act as a receiver from Controller/Access Point. Please enable port <b>9000/udp</b> and <b>Start/Restart</b> to use this service.', 
                'is_enable'     => false,
            ],

            [   'group'         => 'add-on',
                'name'          => 'Omaya Agent : Huawei [BLE]', 
                'service_name'  => 'omaya_agent_huawei_ble', 
                'images'        => 'images/logo.png', 
                'image_styles'  => 'border-radius: 0px !important;max-height: 24px !important;', 
                'remarks'       => 'Omaya Agent for <b>Huawei [ BLE ]</b> act as a receiver from Controller/Access Point. Please enable port <b>9001/udp</b> and <b>Start/Restart</b> to use this service.', 
                'is_enable'     => false,
            ],
            [   'group'         => 'add-on',
                'name'          => 'Omaya Agent : Mikrotik [BLE]', 
                'service_name'  => 'omaya_agent_mikrotik_ble', 
                'images'        => 'images/logo.png', 
                'image_styles'  => 'border-radius: 0px !important;max-height: 24px !important;', 
                'remarks'       => 'Omaya Agent for <b>Mikrotik [ BLE ]</b> act as a receiver from Controller/Access Point.', 
                'is_enable'     => false,
            ],


            [   'group'         => 'add-on',
                'name'          => 'Omaya Agent : Cambium [Wifi]', 
                'service_name'  => 'omaya_agent_cambium_wifi', 
                'images'        => 'images/logo.png', 
                'image_styles'  => 'border-radius: 0px !important;max-height: 24px !important;', 
                'remarks'       => 'Omaya Agent for <b>Cambium [ Wifi ]</b> act as a receiver from Controller/Access Point. Please enable port <b>9005/udp</b> and <b>Start/Restart</b> to use this service.', 
                'is_enable'     => false,
            ],

            [   'group'         => 'add-on',
                'name'          => 'Omaya Agent : Cambium [BLE]', 
                'service_name'  => 'omaya_agent_cambium_ble', 
                'images'        => 'images/logo.png', 
                'image_styles'  => 'border-radius: 0px !important;max-height: 24px !important;', 
                'remarks'       => 'Omaya Agent for <b>Cambium [ BLE ]</b> act as a receiver from Controller/Access Point. Please enable port <b>9006/udp</b> and <b>Start/Restart</b> to use this service.', 
                'is_enable'     => false,
            ]

            

          


        ])
        ->each(function ($service) {

            OmayaSystemService::create([
                'group'             => $service['group'],
                'name'              => $service['name'],
                'service_name'      => $service['service_name'],
                'images'            => $service['images'],
                'image_styles'      => $service['image_styles'],
                'remarks'           => $service['remarks'],
                'is_enable'         => $service['is_enable'],
            ]);


        });
    }
}
