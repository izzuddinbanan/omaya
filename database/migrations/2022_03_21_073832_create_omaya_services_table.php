<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOmayaServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('omaya_system_services', function (Blueprint $table) {
            $table->id();
            $table->char("group", 20)->nullable()->index();
            $table->char("name", 30)->nullable()->index();
            $table->char("service_name", 30)->nullable()->index();
            $table->string("images")->nullable();
            $table->string("image_styles")->nullable();
            $table->boolean("is_enable")->default(false);
            $table->text("remarks")->nullable();
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
        Schema::dropIfExists('omaya_system_services');
    }
}
