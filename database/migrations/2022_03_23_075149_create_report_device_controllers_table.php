<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportDeviceControllersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('omaya_report_device_controller_templates', function (Blueprint $table) {
            $table->id();
            $table->char("tenant_id", 50)->nullable()->index();
            $table->datetime("report_date")->nullable()->index();
            $table->string('mac_address_ap', 50)->nullable()->index();
            $table->bigInteger('packet_total')->default(0);
            $table->bigInteger('packet_accept')->default(0);
            $table->bigInteger('packet_total_wifi')->default(0);
            $table->bigInteger('packet_accept_wifi')->default(0);
            $table->bigInteger('packet_total_ble')->default(0);
            $table->bigInteger('packet_accept_ble')->default(0);
            $table->timestamps();
        });

        Schema::table('omaya_raw_massages', function (Blueprint $table) {
            $table->char('device_controller_report_status', 25)->nullable()->after('is_heatmap_count');
            // $table->string('device_controller_count_column', 50)->nullable()->after('device_controller_report_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('omaya_report_device_controller_templates');

        Schema::table('omaya_raw_massages', function (Blueprint $table) {
            $table->dropColumn([
                'device_controller_report_status',
                // 'device_controller_count_column'
            ]);
        });
    }
}
