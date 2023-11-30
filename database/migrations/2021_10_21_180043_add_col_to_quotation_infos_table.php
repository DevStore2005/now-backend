<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColToQuotationInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotation_infos', function (Blueprint $table) {
            $table->date('date')->nullable()->after('price');
            $table->string('end_lng')->nullable()->after('price');
            $table->string('end_lat')->nullable()->after('price');
            $table->string('start_lng')->nullable()->after('price');
            $table->string('start_lat')->nullable()->after('price');
            $table->string('name')->nullable()->after('price');
            $table->string('email')->nullable()->after('price');
            $table->string('phone')->nullable()->after('price');
            $table->unsignedBigInteger('vehicle_type_id')->nullable()->after('id');
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
                'vehicle_type_id',
                'phone',
                'email',
                'name',
                'start_lat',
                'start_lng',
                'end_lat',
                'end_lng',
                'date'
            ]);
        });
    }
}
