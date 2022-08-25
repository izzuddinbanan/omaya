<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOmayaDeviceBlacklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('omaya_device_lists', function (Blueprint $table) {
            $table->id();
            $table->char("tenant_id", 50)->nullable()->index();
            $table->char('mac_address_device', 12)->nullable()->index();
            $table->boolean('is_blacklist')->default(true)->index();
            $table->text('remark')->nullable();
            $table->integer('created_by')->default('1');
            $table->integer('updated_by')->default('1');
            $table->timestamps();
        });

        Schema::table('omaya_device_histories', function (Blueprint $table) {
            $table->char("tenant_id", 50)->nullable()->index()->after('id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('omaya_device_lists');
        Schema::table('omaya_device_histories', function (Blueprint $table) {
            $table->dropColumn([
                'tenant_id',
            ]);
        });
    }
}
