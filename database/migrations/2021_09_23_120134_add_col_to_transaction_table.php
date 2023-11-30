<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColToTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('service_request_id')->nullable();
            $table->boolean('is_credit')->default(true)->nullable();
            $table->unsignedBigInteger('provider_id')->nullable(true)->change();
            $table->unsignedBigInteger('user_id')->nullable(true)->change();
            $table->string('payment_id')->nullable(true)->change();
            $table->string('amount')->nullable(true)->change();
            $table->string('amount_captured')->nullable(true)->change();
            $table->string('status')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'service_request_id',
                'is_credit',
            ]);
        });
    }
}
