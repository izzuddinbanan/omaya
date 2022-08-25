<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColOmayaCloudTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('omaya_clouds', function (Blueprint $table) {
            $table->integer('delete_log')->nullable()->default(5)->after('timezone');
            $table->boolean('smtp_is_active')->default(false)->after('delete_log');
            $table->char('smtp_host', 50)->nullable()->after('smtp_is_active');
            $table->integer('smtp_port')->nullable()->after('smtp_host');
            $table->char('smtp_auth', 10)->nullable()->after('smtp_port');
            $table->char('smtp_username', 50)->nullable()->after('smtp_auth');
            $table->char('smtp_password', 100)->nullable()->after('smtp_username');
            $table->char('smtp_from_name', 100)->nullable()->after('smtp_password')->default("Omaya");
            $table->char('smtp_from_email', 100)->nullable()->after('smtp_from_name')->default("no-reply@omaya.com");
        });


        Schema::table('omaya_users', function (Blueprint $table) {
            $table->char('location_uid', 50)->nullable()->index()->after('web_mode');
            $table->char('venue_uid', 50)->nullable()->index()->after('location_uid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('omaya_clouds', function (Blueprint $table) {
            $table->dropColumn([
                'delete_log',
                'smtp_is_active',
                'smtp_host',
                'smtp_port',
                'smtp_auth',
                'smtp_username',
                'smtp_password',
                'smtp_from_name',
                'smtp_from_email',
            ]);
        });


        Schema::table('omaya_users', function (Blueprint $table) {
            $table->dropColumn([
                'location_uid',
                'venue_uid'
            ]);
        });
    }
}
