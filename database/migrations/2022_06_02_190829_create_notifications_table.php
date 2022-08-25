<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('omaya_notifications', function (Blueprint $table) {
            $table->id();
            $table->char("tenant_id", 50)->nullable()->index();
            $table->char('notification_uid', 50)->nullable()->index();
            $table->char("rule_uid", 50)->nullable()->index();
            $table->char("location_uid", 50)->nullable()->index();
            $table->char("venue_uid", 50)->nullable()->index();
            $table->char("zone_uid", 50)->nullable()->index();
            $table->char("device_controller_uid", 50)->nullable()->index();
            $table->char("device_tracker_uid", 50)->nullable()->index();
            $table->char("entity_uid", 50)->nullable()->index();
            $table->float("position_x")->nullable();
            $table->float("position_y")->nullable();
            $table->string('trigger_value', 255)->nullable();
            $table->datetime('trigger_at')->nullable();
            $table->string('rssi', 25)->nullable();
            $table->text('message')->nullable();
            $table->char("user_uid", 50)->nullable()->index();
            $table->char('status', 50)->default('new');
            $table->timestamps();
        });

        Schema::create('omaya_notification_histories', function (Blueprint $table) {
            $table->id();
            $table->char("tenant_id", 50)->nullable()->index();
            $table->char('notification_uid', 50)->nullable()->index();
            $table->char('status')->default('new');
            $table->text('message')->nullable();
            $table->char("user_uid", 50)->nullable()->index();
            $table->integer('created_by')->default('1');
            $table->integer('updated_by')->default('1');
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
        Schema::dropIfExists('omaya_notifications');
        Schema::dropIfExists('omaya_notification_histories');
    }
}
