<?php

namespace Database\Seeders;

use App\Models\OmayaUser;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!OmayaUser::first()){

            collect([
                [   'username'      => 'superuser',
                    'tenant_id'     => 'superuser', 
                    'role'          => 'superuser', 
                    'email'         => 'su@synchroweb.com', 
                ],
                [   'username'      => 'admin',
                    'tenant_id'     => 'default', 
                    'role'          => 'admin', 
                    'email'         => 'admin@synchroweb.com', 
                ],


            ])
            ->each(function ($user) {

                OmayaUser::create([
                    'tenant_id'         => $user['tenant_id'],
                    'username'          => $user['username'],
                    'password'  		=> bcrypt('password'),
                    'role'  		    => $user['role'],
                    'email'             => $user['email'],
                    'permission'        => "rw",

                ]);
        

            });
        }
    }
    
}
