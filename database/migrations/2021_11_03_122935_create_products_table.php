<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('restaurant_id')->nullable();
            $table->unsignedBigInteger('grocer_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('type')->nullable();
            $table->string('name')->nullable();
            $table->string('discount_price')->nullable();
            $table->string('price')->nullable();
            $table->string('quantity')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('status')->default('Active');
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
        Schema::dropIfExists('products');
    }
}
