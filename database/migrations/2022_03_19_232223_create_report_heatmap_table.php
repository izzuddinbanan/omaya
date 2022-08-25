<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportHeatmapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        Schema::table('omaya_raw_massages', function (Blueprint $table) {
            $table->char('heatmap_report_status', 25)->nullable()->after('is_engaged');
            $table->boolean('is_heatmap_count')->default(false)->after('heatmap_report_status');
        });

        Schema::create('omaya_report_heatmap_templates', function (Blueprint $table) {
            $table->id();
            $table->char("tenant_id", 50)->nullable()->index();
            $table->datetime("report_date")->nullable()->index();
            $table->char('location_uid', 50)->nullable()->index();
            $table->char('venue_uid', 50)->nullable()->index();
            $table->char('zone_uid', 50)->nullable()->index();
            $table->bigInteger('total')->nullable()->default(0);
            $table->bigInteger('total_wifi')->nullable()->default(0);
            $table->bigInteger('total_ble')->nullable()->default(0);
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
        Schema::dropIfExists('omaya_report_heatmap_templates');

        Schema::table('omaya_raw_massages', function (Blueprint $table) {
            $table->dropColumn([
                'heatmap_report_status',
                'is_heatmap_count',
            ]);
        });
    }
}
