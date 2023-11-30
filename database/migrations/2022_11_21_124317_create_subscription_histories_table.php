<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('providers_subscription_id');
            $table->unsignedBigInteger('service_request_id')->nullable(TRUE);
            $table->unsignedBigInteger('transaction_id')->nullable(TRUE);
            $table->date('deduction_date')->nullable(TRUE);
            $table->decimal('discount', 8, 2)->nullable(TRUE);
            $table->string('status')->nullable(TRUE);
            $table->string('description')->nullable(TRUE);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_histories');
    }
}
