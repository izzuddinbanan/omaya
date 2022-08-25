<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        Schema::create('omaya_groups', function (Blueprint $table) {
            $table->id();
            $table->char("tenant_id", 50)->nullable()->index();
            $table->char("group_uid", 50)->nullable()->index();
            $table->char("name", 50)->nullable()->index();
            $table->text("remark")->nullable();
            $table->integer('created_by')->default('1');
            $table->integer('updated_by')->default('1');
            $table->timestamps();
        });


        Schema::create('omaya_entities', function (Blueprint $table) {
            $table->id();
            $table->char("tenant_id", 50)->nullable()->index();
            $table->char('entity_uid', 50)->nullable()->index();
            $table->char('device_tracker_uid', 50)->nullable()->index();
            $table->char('meet_entity_uid', 50)->nullable()->index();
            $table->char('group_uid', 50)->nullable()->index();
            $table->string('name', 150)->nullable()->index();
            $table->char('type', 50)->nullable()->index();
            $table->text("remarks")->nullable();
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
        Schema::dropIfExists('omaya_groups');
        Schema::dropIfExists('omaya_entities');
    }
}
