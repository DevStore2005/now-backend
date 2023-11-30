<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryIdToBlogsServicesPagesAndOthers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->after('id');
        });
        Schema::table('links', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->after('id');
        });
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->after('id');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->after('id');
        });
        Schema::table('services', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->after('id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('country_id');
        });
        Schema::table('links', function (Blueprint $table) {
            $table->dropColumn('country_id');
        });
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropColumn('country_id');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('country_id');
        });
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('country_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('country_id');
        });
    }
}
