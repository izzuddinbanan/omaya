<?php

namespace Database\Seeders;

use App\Models\OmayaCloud;
use App\Models\OmayaRule;
use Illuminate\Database\Seeder;

class OmayaRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clouds = OmayaCloud::get();

        \DB::table('omaya_rules')->truncate();

        foreach ($clouds as $key => $value) {


            $events = array('battery_level','button_click');

            foreach($events as $event){

                do {

                    $uid = randomStringId();

                } while (OmayaRule::where('rule_uid', $uid)->first());
              
                $default_data = array(
                    'tenant_id'             => $value->tenant_id,
                    'type'                  => "device",
                    'rule_uid'              => $uid,
                    'name'                  => ucwords(str_replace('_', ' ',  $event)),
                    'event'                 => $event,
                    'action'                => 'alert',
                    'send_to_role'          => 'admin',
                    'start_time_action'     => '16:00',
                    'stop_time_action'      => '15:59',
                    'action_every'          => 30,
                    'is_default'            => 1,
                );

                if($event == 'battery_level'){
                    $default_data['comparison']    = 'less_than';
                    $default_data['value']          = 10;
                }

                OmayaRule::create($default_data);
            }

        }
        
    }
}
