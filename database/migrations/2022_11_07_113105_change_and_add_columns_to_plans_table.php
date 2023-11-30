<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAndAddColumnsToPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('title')->nullable(true)->change();
            $table->string('identifier')->nullable(true)->change();
            $table->string('stripe_id')->nullable(true)->change();
            $table->string('stripe_name')->nullable(true)->change();
            $table->string('credit')->after('price')->nullable(true);
            $table->string('threshold')->after('credit')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'leads',
                'threshold'
            ]);
        });
    }
}
