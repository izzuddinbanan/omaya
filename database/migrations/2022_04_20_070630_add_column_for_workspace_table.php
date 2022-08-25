<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnForWorkspaceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('omaya_modules', function (Blueprint $table) {
            $table->string('module_for', 100)->nullable()->after('is_superuser')->default('crowd');
        });

        Schema::table('omaya_clouds', function (Blueprint $table) {
            $table->boolean('is_use_server_time')->after('remove_dwell_time')->default(true);
        });


        Schema::create('omaya_device_trackers', function (Blueprint $table) {
            $table->id();
            $table->char("tenant_id", 50)->nullable()->index();
            $table->char('device_uid', 50)->nullable()->index();
            $table->string('name', 100)->nullable()->index();
            $table->char('mac_address', 30)->nullable()->index();
            $table->char('mac_address_separator', 30)->nullable()->index();
            $table->text('remarks')->nullable();
            $table->dateTime('last_seen_at')->nullable();
            $table->char('last_location_uid', 50)->nullable();
            $table->char('last_venue_uid', 50)->nullable();
            $table->char('last_zone_uid', 50)->nullable();
            $table->char('last_rssi', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('status', 15)->nullable();
            $table->boolean('is_allocated')->default(false);
            $table->integer("created_by")->default("1");
            $table->integer("updated_by")->default("1");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('omaya_modules', function (Blueprint $table) {
            $table->dropColumn([
                'module_for',
            ]);
        });

        Schema::table('omaya_clouds', function (Blueprint $table) {
            $table->dropColumn([
                'is_use_server_time',
            ]);
        });

        Schema::dropIfExists('omaya_device_trackers');

    }
}
