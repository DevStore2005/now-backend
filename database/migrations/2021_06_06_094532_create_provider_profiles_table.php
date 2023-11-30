<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProviderProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provider_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id');
            $table->string('dob')->nullable();
            $table->string('street_address')->nullable();
            $table->string('suite_number')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();

            $table->string('business_name')->nullable();
            $table->string('founded_year')->nullable();
            $table->string('number_of_employees')->nullable();
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
        Schema::dropIfExists('provider_profiles');
    }
}
