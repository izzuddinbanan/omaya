<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOmayaReportGeneralTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('omaya_raw_massages', function (Blueprint $table) {
            $table->char('general_report_status', 25)->nullable()->after('dwell_report_table');
            $table->char('general_last', 25)->nullable()->after('general_report_status');
            $table->char('general_now', 25)->nullable()->after('general_last');
            $table->string('general_report_table', 50)->nullable()->after('general_now');
            $table->boolean('general_return_device')->default(false)->after('general_report_table');
        });


        Schema::create('omaya_report_general_templates', function (Blueprint $table) {
            $table->id();
            $table->char("tenant_id", 50)->nullable()->index();
            $table->datetime("report_date")->nullable()->index();
            $table->char('location_uid', 50)->nullable()->index();
            $table->char('venue_uid', 50)->nullable()->index();
            $table->char('zone_uid', 50)->nullable()->index();
            $table->bigInteger('total')->nullable()->default(0);
            $table->bigInteger('unique_total')->nullable()->default(0);
            $table->bigInteger('passby')->nullable()->default(0);
            $table->bigInteger('unique_passby')->nullable()->default(0);
            $table->bigInteger('visit')->nullable()->default(0);
            $table->bigInteger('unique_visit')->nullable()->default(0);
            $table->bigInteger('engaged')->nullable()->default(0);
            $table->bigInteger('unique_engaged')->nullable()->default(0);
            $table->bigInteger('new_device')->nullable()->default(0);
            $table->bigInteger('return_device')->nullable()->default(0);


            $table->bigInteger('total_wifi')->nullable()->default(0);
            $table->bigInteger('unique_total_wifi')->nullable()->default(0);
            $table->bigInteger('passby_wifi')->nullable()->default(0);
            $table->bigInteger('unique_passby_wifi')->nullable()->default(0);
            $table->bigInteger('visit_wifi')->nullable()->default(0);
            $table->bigInteger('unique_visit_wifi')->nullable()->default(0);
            $table->bigInteger('engaged_wifi')->nullable()->default(0);
            $table->bigInteger('unique_engaged_wifi')->nullable()->default(0);
            $table->bigInteger('new_device_wifi')->nullable()->default(0);
            $table->bigInteger('return_device_wifi')->nullable()->default(0);



            $table->bigInteger('total_ble')->nullable()->default(0);
            $table->bigInteger('unique_total_ble')->nullable()->default(0);
            $table->bigInteger('passby_ble')->nullable()->default(0);
            $table->bigInteger('unique_passby_ble')->nullable()->default(0);
            $table->bigInteger('visit_ble')->nullable()->default(0);
            $table->bigInteger('unique_visit_ble')->nullable()->default(0);
            $table->bigInteger('engaged_ble')->nullable()->default(0);
            $table->bigInteger('unique_engaged_ble')->nullable()->default(0);
            $table->bigInteger('new_device_ble')->nullable()->default(0);
            $table->bigInteger('return_device_ble')->nullable()->default(0);

            
            
            $table->timestamps();
        });

        Schema::create('omaya_device_histories', function (Blueprint $table) {
            $table->id();
            $table->date("report_date")->nullable()->index();
            $table->char('location_uid', 50)->nullable()->index();
            $table->char('venue_uid', 50)->nullable()->index();
            $table->char('zone_uid', 50)->nullable()->index();
            $table->char('mac_address_device', 12)->nullable()->index();
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
        Schema::dropIfExists('omaya_report_general_templates');
        Schema::dropIfExists('omaya_device_histories');

        Schema::table('omaya_raw_massages', function (Blueprint $table) {
            $table->dropColumn([
                'general_report_status',
                'general_last',
                'general_now',
                'general_report_table',
                'general_return_device',
            ]);
        });
    }
}
