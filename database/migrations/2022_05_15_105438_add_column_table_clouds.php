<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnTableClouds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('omaya_clouds', function (Blueprint $table) {
            $table->string('location_image',100)->after('is_use_server_time')->nullable();
            $table->integer('location_image_width')->after('location_image')->nullable();
            $table->integer('location_image_height')->after('location_image_width')->nullable();
        });

        Schema::table('omaya_locations', function (Blueprint $table) {
            $table->float('position_x')->after('remark')->nullable();
            $table->float('position_y')->after('position_x')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('omaya_clouds', function (Blueprint $table) {
            $table->dropColumn([
                'location_image',
                'location_image_width',
                'location_image_height',
            ]);
        });

        Schema::table('omaya_locations', function (Blueprint $table) {
            $table->dropColumn([
                'position_x',
                'position_y',
            ]);
        });
    }
}
