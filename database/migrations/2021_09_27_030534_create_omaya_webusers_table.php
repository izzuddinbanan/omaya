<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOmayaWebusersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('omaya_users', function (Blueprint $table) {
            $table->id();
            $table->char('tenant_id', 25)->nullable()->index();
            $table->string('email', 50)->nullable();
            $table->string('username', 50)->nullable()->index();
            $table->string('password')->nullable();
            $table->string('allowed_tenant_id')->nullable();
            $table->string('role', 50)->nullable();
            $table->string('permission', 5)->nullable();
            $table->string('reset_key', 50)->nullable();
            $table->string('web_mode', 20)->nullable()->default('light-layout');
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
        Schema::dropIfExists('omaya_users');
    }
}
