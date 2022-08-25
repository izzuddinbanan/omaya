<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrossVisitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('omaya_report_cross_visit_templates', function (Blueprint $table) {
            $table->id();
            $table->char("tenant_id", 50)->nullable()->index();
            $table->datetime("report_date")->nullable()->index();
            $table->char('location_uid', 50)->nullable()->index();
            $table->char('venue_uid', 50)->nullable()->index();
            $table->char('zone_uid', 50)->nullable()->index();
            $table->char('to_location_uid', 50)->nullable()->index();
            $table->char('to_venue_uid', 50)->nullable()->index();
            $table->char('to_zone_uid', 50)->nullable()->index();
            $table->bigInteger('total')->default(0);
            $table->bigInteger('total_wifi')->default(0);
            $table->bigInteger('total_ble')->default(0);
            $table->timestamps();
        });

        Schema::table('omaya_raw_massages', function (Blueprint $table) {
            $table->char('cross_visit_report_status', 25)->nullable()->after('device_controller_report_status');
            $table->char('previous_mac_address_ap', 50)->nullable()->index()->after('cross_visit_report_status');
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
        Schema::dropIfExists('omaya_report_cross_visit_templates');

        Schema::table('omaya_raw_massages', function (Blueprint $table) {
            $table->dropColumn([
                'cross_visit_report_status',
                'previous_mac_address_ap',
            ]);
        });
    }
}
