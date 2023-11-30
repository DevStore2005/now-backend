<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStartAndAddressToQuotationInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotation_infos', function (Blueprint $table) {
            $table->string('from_address')->nullable()->after('name');
            $table->string('to_address')->nullable()->after('start_lng');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotation_infos', function (Blueprint $table) {
            $table->dropColumn([
                'from_address',
                'to_address'
            ]);
        });
    }
}
