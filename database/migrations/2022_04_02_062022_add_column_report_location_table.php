<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnReportLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('omaya_report_cross_visit_templates', function (Blueprint $table) {
            $table->string('location_name', 200)->nullable()->after('total_ble');
            $table->string('venue_name', 200)->nullable()->after('location_name');
            $table->string('zone_name', 200)->nullable()->after('venue_name');
            $table->string('to_location_name', 200)->nullable()->after('zone_name');
            $table->string('to_venue_name', 200)->nullable()->after('to_location_name');
            $table->string('to_zone_name', 200)->nullable()->after('to_venue_name');
        });

        Schema::table('omaya_report_dwell_templates', function (Blueprint $table) {
            $table->string('location_name', 200)->nullable()->after('dwell_more_ble');
            $table->string('venue_name', 200)->nullable()->after('location_name');
            $table->string('zone_name', 200)->nullable()->after('venue_name');
        });

        Schema::table('omaya_report_general_templates', function (Blueprint $table) {
            $table->string('location_name', 200)->nullable()->after('return_device_ble');
            $table->string('venue_name', 200)->nullable()->after('location_name');
            $table->string('zone_name', 200)->nullable()->after('venue_name');
        });

        Schema::table('omaya_report_heatmap_templates', function (Blueprint $table) {
            $table->string('location_name', 200)->nullable()->after('total_ble');
            $table->string('venue_name', 200)->nullable()->after('location_name');
            $table->string('zone_name', 200)->nullable()->after('venue_name');
        });



        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('omaya_report_cross_visit_templates', function (Blueprint $table) {
            $table->dropColumn([
                'location_name',
                'venue_name',
                'zone_name',
                'to_location_name',
                'to_venue_name',
                'to_zone_name',
            ]);
        });

        Schema::table('omaya_report_dwell_templates', function (Blueprint $table) {
            $table->dropColumn([
                'location_name',
                'venue_name',
                'zone_name',
            ]);
        });

        Schema::table('omaya_report_general_templates', function (Blueprint $table) {
            $table->dropColumn([
                'location_name',
                'venue_name',
                'zone_name',
            ]);
        });


        Schema::table('omaya_report_heatmap_templates', function (Blueprint $table) {
            $table->dropColumn([
                'location_name',
                'venue_name',
                'zone_name',
            ]);
        });

        
    }
}
