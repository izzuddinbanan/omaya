<?php

namespace Database\Seeders;

use App\Models\OmayaCloud;
use App\Models\OmayaModule;
use App\Models\OmayaRole;
use Illuminate\Database\Seeder;

require_once dirname(__FILE__, 3) . "/app/Supports/helper.php";


class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $tenant_id = 'default';
        system("chown -R root:root /var/www/omaya/");
        \Artisan::call('config:cache');
        \Artisan::call('view:cache');

        \DB::table('omaya_modules')->truncate();
        \DB::table('omaya_roles')->truncate();
        \DB::table('omaya_oui_standards')->truncate();

        // collect([

        //     //Dashboard
        //     [   'name'          => 'general:dashboard',
        //         'group'         => 'general', 
        //     ],


        //     //Analytics
        //     [   'name'          => 'analytics:benchmark',
        //         'group'         => 'analytics', 
        //     ],
        //     [   'name'          => 'analytics:heatmap',
        //         'group'         => 'analytics', 
        //     ],
        //     [   'name'          => 'analytics:cross-visit',
        //         'group'         => 'analytics', 
        //     ],
        //     [   'name'          => 'analytics:cross-path',
        //         'group'         => 'analytics', 
        //     ],
        //     [   'name'          => 'analytics:dwell-time',
        //         'group'         => 'analytics', 
        //     ],
        //     [   'name'          => 'analytics:enter-exit',
        //         'group'         => 'analytics', 
        //     ],
        //     [   'name'          => 'analytics:loyalty-distribution',
        //         'group'         => 'analytics', 
        //     ],
        //     [   'name'          => 'analytics:unique-visit',
        //         'group'         => 'analytics', 
        //     ],
        //     [   'name'          => 'analytics:visit',
        //         'group'         => 'analytics', 
        //     ],
        //     [   'name'          => 'analytics:venue-map',
        //         'group'         => 'analytics', 
        //     ],
            
           

        //     //Monitor
        //     [   'name'          => 'monitor:device-ap',
        //         'group'         => 'monitor', 
        //     ],
        //     [   'name'          => 'monitor:device-tracker',
        //         'group'         => 'monitor', 
        //     ],
        //     [   'name'          => 'monitor:venue',
        //         'group'         => 'monitor', 
        //     ],
        //     [   'name'          => 'monitor:service',
        //         'group'         => 'monitor', 
        //     ],
        //     [   'name'          => 'monitor:scheduler',
        //         'group'         => 'monitor', 
        //     ],


        //     //Manage
        //     [   'name'          => 'manage:location',
        //         'group'         => 'manage', 
        //     ],
        //     [   'name'          => 'manage:venue',
        //         'group'         => 'manage', 
        //     ],
        //     [   'name'          => 'manage:zone',
        //         'group'         => 'manage', 
        //     ],
        //     [   'name'          => 'manage:device-ap',
        //         'group'         => 'manage', 
        //     ],
        //     [   'name'          => 'manage:device-tracker',
        //         'group'         => 'manage', 
        //     ],

        //     //Help Tools
        //     [   'name'          => 'help:service',
        //         'group'         => 'help', 
        //         'is_superuser'  => true, 
        //     ],



        //     //Setting
        //     [   'name'          => 'setting:role',
        //         'group'         => 'setting', 
        //     ],
        //     [   'name'          => 'setting:user',
        //         'group'         => 'setting', 
        //     ],
        //     [   'name'          => 'setting:config',
        //         'group'         => 'setting', 
        //     ],
        //     [   'name'          => 'setting:filtering',
        //         'group'         => 'setting', 
        //     ],
        //     [   'name'          => 'setting:service',
        //         'group'         => 'setting', 
        //         'is_superuser'  => true, 
        //     ],
            
        //     //Cloud
        //     [   'name'          => 'cloud:tenant',
        //         'group'         => 'cloud', 
        //         'is_superuser'  => true, 
        //     ],


        // ])
        // ->each(function ($module)  use($tenant_id){

        //     OmayaModule::create([
        //         'name'              => $module['name'],
        //         'group'             => $module['group'],
        //         'is_superuser'      => $module['is_superuser'] ?? false,
        //     ]);

        //     if(($module['is_superuser'] ?? false) == false) {

        //         OmayaRole::create([
        //             'name'          => 'admin',
        //             'module_id'     => $module['name'],
        //             'tenant_id'     => $tenant_id,
        //         ]);
        //     }


        // });


        \Artisan::call('storage:link');



        $path = storage_path("app/public/tenants/default");

        \File::deleteDirectory(storage_path("app/public/"));

        // TENANT LICENSE
        if (!\File::isDirectory($path)) {

            \File::makeDirectory($path, 0775, true);
        }

        $omy_license = generateTempLicense();

        file_put_contents("{$path}/tenant.license",  $omy_license, FILE_APPEND);



        OmayaCloud::create([
            'tenant_id'     => "default",
            'name'          => "Default",
            'is_active'     => true,
            'is_filter_oui' => true,
            'is_filter_mac_random' => true,
            'is_filter_dwell_time' => true,
            'license_key'   => $omy_license,
            'expired_at'    => date('Y-m-d', strtotime(date("Y-m-d") . ' + 10 days')),

        ]);


        // OUI TABLE
        pullOuiStandard();

        system("sudo chown -R nginx:nginx /var/www/omaya/storage/");
        system("sudo chmod -R 755 /var/www/omaya/storage/");
        system("sudo chown -R nginx:nginx /var/www/omaya/bootstrap/");
        exec('rm -rf ' . storage_path('logs/*'));


        // \Artisan::call('omaya:service');






    }


}
