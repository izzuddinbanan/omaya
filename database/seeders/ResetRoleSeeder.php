<?php

namespace Database\Seeders;

use App\Models\OmayaCloud;
use App\Models\OmayaModule;
use App\Models\OmayaRole;
use Illuminate\Database\Seeder;

class ResetRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        $clouds = OmayaCloud::get();

        \DB::table('omaya_modules')->truncate();
        \DB::table('omaya_roles')->truncate();



        foreach ($clouds as $cloud) {

            collect([

                //Dashboard
                [   'name'          => 'general:dashboard',
                    'group'         => 'general', 
                ],


                //Analytics
                [   'name'          => 'analytics:benchmark',
                    'group'         => 'analytics', 
                ],
                [   'name'          => 'analytics:heatmap',
                    'group'         => 'analytics', 
                ],
                [   'name'          => 'analytics:cross-visit',
                    'group'         => 'analytics', 
                ],
                [   'name'          => 'analytics:cross-path',
                    'group'         => 'analytics', 
                ],
                [   'name'          => 'analytics:dwell-time',
                    'group'         => 'analytics', 
                ],
                [   'name'          => 'analytics:enter-exit',
                    'group'         => 'analytics', 
                ],
                [   'name'          => 'analytics:loyalty-distribution',
                    'group'         => 'analytics', 
                ],
                [   'name'          => 'analytics:unique-visit',
                    'group'         => 'analytics', 
                ],
                [   'name'          => 'analytics:visit',
                    'group'         => 'analytics', 
                ],
                [   'name'          => 'analytics:venue-map',
                    'group'         => 'analytics', 
                ],
                
               

                //Monitor
                [   'name'          => 'monitor:device-ap',
                    'group'         => 'monitor', 
                ],
                [   'name'          => 'monitor:device-tracker',
                    'group'         => 'monitor', 
                ],
                [   'name'          => 'monitor:venue',
                    'group'         => 'monitor', 
                ],
                [   'name'          => 'monitor:service',
                    'group'         => 'monitor', 
                ],
                [   'name'          => 'monitor:scheduler',
                    'group'         => 'monitor', 
                ],


                //Manage
                [   'name'          => 'manage:location',
                    'group'         => 'manage', 
                ],
                [   'name'          => 'manage:venue',
                    'group'         => 'manage', 
                ],
                [   'name'          => 'manage:zone',
                    'group'         => 'manage', 
                ],
                [   'name'          => 'manage:device-ap',
                    'group'         => 'manage', 
                ],
                [   'name'          => 'manage:device-tracker',
                    'group'         => 'manage', 
                ],
                [   'name'          => 'manage:group',
                    'group'         => 'manage', 
                    'module_for'    => 'workspace',
                ],
                [   'name'          => 'manage:entity',
                    'group'         => 'manage', 
                    'module_for'    => 'workspace',
                ],
                [   'name'          => 'manage:rule',
                    'group'         => 'manage', 
                    'module_for'    => 'workspace',
                ],


                //Help Tools
                [   'name'          => 'help:service',
                    'group'         => 'help', 
                    'is_superuser'  => true, 
                ],
                [   'name'          => 'help:device-blacklist',
                    'group'         => 'help', 
                ],



                //Setting
                [   'name'          => 'setting:role',
                    'group'         => 'setting', 
                ],
                [   'name'          => 'setting:user',
                    'group'         => 'setting', 
                ],
                [   'name'          => 'setting:config',
                    'group'         => 'setting', 
                ],
                [   'name'          => 'setting:filtering',
                    'group'         => 'setting', 
                ],
                [   'name'          => 'setting:service',
                    'group'         => 'setting', 
                    'is_superuser'  => true, 
                ],
                
                //Cloud
                [   'name'          => 'cloud:tenant',
                    'group'         => 'cloud', 
                    'is_superuser'  => true, 
                ],


            ])
            ->each(function ($module)  use($cloud){

                OmayaModule::create([
                    'name'              => $module['name'],
                    'group'             => $module['group'],
                    'module_for'        => $module['module_for'] ?? 'general',
                    'is_superuser'      => $module['is_superuser'] ?? false,
                ]);

                if(($module['is_superuser'] ?? false) == false) {

                    OmayaRole::create([
                        'name'          => 'admin',
                        'module_id'     => $module['name'],
                        'tenant_id'     => $cloud->tenant_id,
                    ]);
                }


            });


        }
        
    }
}
