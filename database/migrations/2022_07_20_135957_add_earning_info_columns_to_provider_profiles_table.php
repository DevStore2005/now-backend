<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEarningInfoColumnsToProviderProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('provider_profiles', function (Blueprint $table) {
            $table->index('provider_id');
            $table->string('total_earn')->after('provider_id')->default(0);
            $table->string('commission')->after('provider_id')->default(0);
            $table->string('earn')->after('provider_id')->default(0);
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
            $table->dropColumn([
                'earn',
                'total_earn',
                'commission',
            ]);
            $table->dropIndex('provider_profiles_provider_id_index');
        });
    }
}
