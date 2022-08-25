<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOmayaVenuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('omaya_locations', function (Blueprint $table) {
            $table->id();
            $table->char('tenant_id', 50)->nullable()->index();
            $table->char('location_uid', 50)->nullable()->index();
            $table->string('name')->nullable()->index();
            $table->text('address')->nullable();
            $table->string('remark')->nullable();
            $table->integer('created_by')->default('1');
            $table->integer('updated_by')->default('1');
            $table->timestamps();

        });
        Schema::create('omaya_venues', function (Blueprint $table) {
            $table->id();
            $table->char('tenant_id', 50)->nullable()->index();
            $table->char('location_uid', 50)->nullable()->index();
            $table->char('venue_uid', 50)->nullable()->index();
            $table->string('name', 200)->nullable()->index();
            $table->string('level', 10)->nullable();
            $table->string('address', 250)->nullable();
            $table->string('image', 250)->nullable();
            $table->integer('image_width')->nullable();
            $table->integer('image_height')->nullable();
            $table->text('space_length_point')->nullable();
            $table->integer('space_length_meter')->nullable();
            $table->integer('space_length_px')->nullable();
            $table->integer('rssi_min')->default('-70');
            $table->integer('rssi_max')->default('-50');
            $table->integer('rssi_min_ble')->default('-60');
            $table->integer('rssi_max_ble')->default('-40');
            $table->integer('dwell_time')->nullable();
            $table->boolean("is_default_dashboard")->default(0);
            $table->integer("created_by")->default("1");
            $table->integer("updated_by")->default("1");
            $table->timestamps();

        });


        Schema::create('omaya_zones', function (Blueprint $table) {
            $table->id();
            $table->char('tenant_id', 50)->nullable()->index();
            $table->char('location_uid', 50)->nullable()->index();
            $table->char('venue_uid', 50)->nullable()->index();
            $table->char('zone_uid', 50)->nullable()->index();
            $table->string('name', 200)->nullable()->index();
            $table->text('remark')->nullable();
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
        Schema::dropIfExists('omaya_locations');
        Schema::dropIfExists('omaya_venues');
        Schema::dropIfExists('omaya_zones');
    }
}
