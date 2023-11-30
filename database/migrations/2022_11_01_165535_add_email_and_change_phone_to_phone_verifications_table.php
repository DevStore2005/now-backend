<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailAndChangePhoneToPhoneVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('phone_verifications', function (Blueprint $table) {
            $table->string('email')->nullable(true)->after('id');
            $table->string('phone')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('phone_verifications', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
}
