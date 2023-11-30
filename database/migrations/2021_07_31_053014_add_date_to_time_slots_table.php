<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateToTimeSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('time_slots', function (Blueprint $table) {
            $table->unsignedBigInteger('provider_schedule_id')->nullable()->change();
            $table->unsignedBigInteger('service_request_id')->nullable()->after('provider_schedule_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('time_slots', function (Blueprint $table) {
            $table->dropColumn(['service_request_id']);
        });
    }
}
