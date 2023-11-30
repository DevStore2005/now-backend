<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTimeNullableToBlockedSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blocked_slots', function (Blueprint $table) {
            $table->string('from_time')->nullable(true)->change();
            $table->string('to_time')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blocked_slots', function (Blueprint $table) {
            //
        });
    }
}
