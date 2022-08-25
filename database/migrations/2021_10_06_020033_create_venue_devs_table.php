<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVenueDevsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('omaya_device_controllers', function (Blueprint $table) {
            $table->id();
            $table->char('tenant_id', 50)->nullable()->index();
            $table->char('location_uid', 50)->nullable()->index();
            $table->char('venue_uid', 50)->nullable()->index();
            $table->char('zone_uid', 50)->nullable()->index();
            $table->char('device_uid', 50)->nullable()->index();
            $table->string('name', 100)->nullable()->index();
            $table->char('mac_address', 30)->nullable()->index();
            $table->char('mac_address_separator', 30)->nullable()->index();
            $table->char('device_type', 20)->nullable();
            $table->float("position_x")->nullable();
            $table->float("position_y")->nullable();
            $table->integer('rssi_min')->nullable();
            $table->integer('rssi_max')->nullable();
            $table->integer('rssi_min_ble')->nullable();
            $table->integer('rssi_max_ble')->nullable();
            $table->integer('dwell_time')->nullable();
            $table->boolean('is_default_setting')->default(true);
            $table->integer("created_by")->default("1");
            $table->integer("updated_by")->default("1");
            $table->dateTime('last_seen_at')->nullable();
            $table->string('status', 15)->nullable();
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('omaya_device_controllers');
    }
}
