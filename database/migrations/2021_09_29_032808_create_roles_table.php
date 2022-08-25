<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('omaya_roles', function (Blueprint $table) {
            $table->id();
            $table->char('tenant_id', 100)->nullable()->index();
            $table->string('name', 100)->nullable()->index();
            $table->string('module_id', 100)->nullable()->index();
            // $table->boolean('is_superuser')->default(false)->nullable();
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
        Schema::dropIfExists('omaya_roles');
    }
}
