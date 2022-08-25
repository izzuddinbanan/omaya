<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOmayaRawData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('omaya_raw_data', function (Blueprint $table) {
            $table->id();
            $table->char("tenant_id", 50)->nullable()->index();
            $table->string('mac_address_ap', 50)->nullable()->index();
            $table->string('mac_address_device', 50)->nullable()->index();
            $table->string('device_vendor', 100)->nullable()->index();
            $table->string('rssi', 25)->nullable();
            $table->string('rssi_type', 10)->nullable()->default('wifi');
            $table->datetime('seen_at')->nullable();
            // $table->datetime('last_seen')->nullable();
            $table->char('venue_uid', 50)->nullable()->index();
            $table->text('raw_data')->nullable();
            $table->char('processing_id', 15)->nullable()->index();
            $table->timestamps();
        });

        Schema::create('omaya_raw_massages', function (Blueprint $table) {
            $table->id();
            $table->char("tenant_id", 50)->nullable()->index();
            $table->string('mac_address_ap', 50)->nullable()->index();
            $table->string('mac_address_device', 50)->nullable()->index();
            $table->string('device_vendor', 100)->nullable()->index();
            $table->string('rssi', 25)->nullable();
            $table->string('rssi_type', 10)->nullable()->default('wifi');
            $table->datetime('first_seen_at')->nullable();
            $table->datetime('last_seen_at')->nullable();
            $table->char('location_uid', 50)->nullable()->index();
            $table->char('venue_uid', 50)->nullable()->index();
            $table->char('zone_uid', 50)->nullable()->index();
            $table->text('raw_data')->nullable();
            // $table->char('processing_id', 25)->nullable()->index();
            // $table->char('current_dwell', 25)->nullable()->index();
            // $table->char('next_dwell', 25)->nullable()->index();
            $table->char('dwell_report_status', 25)->nullable();
            // $table->char('dwell_current', 25)->nullable()->index();
            // $table->char('dwell_next', 25)->nullable()->index();
            $table->bigInteger('dwell_last')->nullable();
            $table->bigInteger('dwell_now')->nullable();
            $table->char('dwell_group_last', 20)->nullable();
            $table->char('dwell_group_now', 20)->nullable();
            $table->string('dwell_report_table', 50)->nullable();
            $table->boolean('is_engaged')->default(false)->nullable();
            $table->datetime('report_time')->nullable();
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
        Schema::dropIfExists('omaya_raw_data');
        Schema::dropIfExists('omaya_raw_massages');
    }
}
