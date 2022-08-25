<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(RoleTableSeeder::class);
        $this->call(OmayaServiceSeeder::class);
        $this->call(ResetRoleSeeder::class);
        $this->call(OmayaRuleSeeder::class);
    }
}
