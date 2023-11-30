<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('restaurant_id')->nullable();
            $table->unsignedBigInteger('grocer_id')->nullable();
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('food_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->string('type');
            $table->string('status');
            $table->string('address');
            $table->string('description')->nullable();
            $table->string('price')->nullable();
            $table->string('total_amount');
            $table->string('quantity');
            $table->string('discount')->nullable();
            $table->string('payment_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
