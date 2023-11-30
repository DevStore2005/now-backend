<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableBusinessProfile extends Migration
{ 
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_profile', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('country')->nullable();
            $table->string('business_phone');
            $table->string('website')->nullable();
            $table->string('type')->nullable();
            $table->string('restaurant_type')->nullable();
            $table->string('profile_image')->nullable();
            $table->text('about')->nullable();
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
        Schema::dropIfExists('business_profile');
    }
}
