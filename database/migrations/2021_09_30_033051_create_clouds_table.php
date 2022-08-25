<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCloudsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('omaya_clouds', function (Blueprint $table) {
            $table->id();
            $table->char("tenant_id", 50)->nullable()->index();
            $table->boolean("is_active")->default(false);
            $table->text("license_key")->nullable();
            $table->string("name", 100)->nullable()->index();
            $table->text("address")->nullable();
            $table->string("phone", 20)->nullable();
            $table->string("email", 200)->nullable();
            $table->string("timezone")->nullable()->default("Asia/Kuala_Lumpur")->index();
            $table->boolean("is_filter_oui")->default(true);
            $table->boolean("is_filter_mac_random")->default(true);
            $table->boolean("is_filter_dwell_time")->default(false);
            $table->integer("remove_dwell_time")->default(1)->nullable();
            $table->dateTime('expired_at')->nullable();
            $table->integer("created_by")->default("1");
            $table->integer("updated_by")->default("1");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('omaya_clouds');
    }
}
