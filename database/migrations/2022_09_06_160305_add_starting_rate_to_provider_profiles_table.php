<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStartingRateToProviderProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('provider_profiles', function (Blueprint $table) {
            $table->unsignedDecimal('starting_rate', 8, 2)->nullable()->after('hourly_rate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('provider_profiles', function (Blueprint $table) {
            $table->dropColumn('starting_rate');
        });
    }
}
