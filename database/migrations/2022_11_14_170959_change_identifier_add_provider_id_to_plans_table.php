<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeIdentifierAddProviderIdToPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('identifier');
            $table->unsignedBigInteger('provider_id')->after('id')->nullable();
            $table->string('type')->after('credit')->nullable();
            $table->unsignedTinyInteger('duration')->after('type')->nullable();
            $table->unsignedTinyInteger('off')->after('duration')->nullable();
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
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'provider_id',
                'type',
                'duration',
                'off'
            ]);
        });
    }
}
