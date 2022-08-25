<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOmayaReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('omaya_report_dwell_templates', function (Blueprint $table) {
            $table->id();
            $table->char("tenant_id", 50)->nullable()->index();
            $table->datetime("report_date")->nullable()->index();
            $table->char('location_uid', 50)->nullable()->index();
            $table->char('venue_uid', 50)->nullable()->index();
            $table->char('zone_uid', 50)->nullable()->index();

            $table->bigInteger('total_dwell')->nullable()->default(0);
            $table->bigInteger('total_dwell_engaged')->nullable()->default(0);
            $table->bigInteger('dwell_15')->nullable()->default(0);
            $table->bigInteger('dwell_30')->nullable()->default(0);
            $table->bigInteger('dwell_60')->nullable()->default(0);
            $table->bigInteger('dwell_120')->nullable()->default(0);
            $table->bigInteger('dwell_240')->nullable()->default(0);
            $table->bigInteger('dwell_480')->nullable()->default(0);
            $table->bigInteger('dwell_more')->nullable()->default(0);



            $table->bigInteger('total_dwell_wifi')->nullable()->default(0);
            $table->bigInteger('total_dwell_engaged_wifi')->nullable()->default(0);
            $table->bigInteger('dwell_15_wifi')->nullable()->default(0);
            $table->bigInteger('dwell_30_wifi')->nullable()->default(0);
            $table->bigInteger('dwell_60_wifi')->nullable()->default(0);
            $table->bigInteger('dwell_120_wifi')->nullable()->default(0);
            $table->bigInteger('dwell_240_wifi')->nullable()->default(0);
            $table->bigInteger('dwell_480_wifi')->nullable()->default(0);
            $table->bigInteger('dwell_more_wifi')->nullable()->default(0);

            
            $table->bigInteger('total_dwell_ble')->nullable()->default(0);
            $table->bigInteger('total_dwell_engaged_ble')->nullable()->default(0);
            $table->bigInteger('dwell_15_ble')->nullable()->default(0);
            $table->bigInteger('dwell_30_ble')->nullable()->default(0);
            $table->bigInteger('dwell_60_ble')->nullable()->default(0);
            $table->bigInteger('dwell_120_ble')->nullable()->default(0);
            $table->bigInteger('dwell_240_ble')->nullable()->default(0);
            $table->bigInteger('dwell_480_ble')->nullable()->default(0);
            $table->bigInteger('dwell_more_ble')->nullable()->default(0);
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
        Schema::dropIfExists('omaya_report_dwell_templates');
    }
}
