<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmountToServiceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->string('sub_service')->nullable();
            $table->boolean('payment_status')->nullable();
            $table->string('paid_amount')->nullable();
            $table->string('payable_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn([
                'paid_amount',
                'payable_amount',
                'amount',
                'sub_service',
                'payment_status'
            ]);
        });
    }
}
