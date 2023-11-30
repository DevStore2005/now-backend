<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServiceRequestIdToProvidersSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('providers_subscriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('service_request_id')->nullable()->after('plan_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('providers_subscriptions', function (Blueprint $table) {
            $table->dropColumn('service_request_id');
        });
    }
}
