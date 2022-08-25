<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOmayaRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('omaya_rules', function (Blueprint $table) {
            $table->id();
            $table->char("tenant_id", 50)->nullable()->index();
            $table->char("rule_uid", 50)->nullable()->index();
            $table->char("name", 50)->nullable();
            $table->char("type", 20)->nullable();
            $table->char("identifier", 60)->nullable()->index();
            $table->char("event", 60)->nullable()->index();
            $table->char("comparison", 20)->nullable();
            $table->char("value", 60)->nullable();
            $table->char("location_uid", 60)->nullable()->index();
            $table->char("venue_uid", 60)->nullable()->index();
            $table->char("zone_uid", 60)->nullable()->index();
            $table->integer("priority")->default('1');
            $table->char("action", 20)->nullable();
            $table->integer("action_every")->default('1200');
            $table->char("send_to_role", 60)->nullable();
            $table->char("start_time_action", 20)->nullable();
            $table->char("stop_time_action", 20)->nullable();
            $table->integer("is_default")->default('0');
            $table->integer("is_active")->default('1');
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
        Schema::dropIfExists('omaya_rules');
    }
}
