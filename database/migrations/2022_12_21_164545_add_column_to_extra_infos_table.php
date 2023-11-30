<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToExtraInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('extra_infos', function (Blueprint $table) {
            $table->string('url')->nullable(True)->after('image');
            $table->string('title_1')->nullable(True)->after('url');
            $table->text('description_1')->nullable(True)->after('title_1');
            $table->string('title_2')->nullable(True)->after('description_1');
            $table->text('description_2')->nullable(True)->after('description_1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('extra_infos', function (Blueprint $table) {
            $table->dropColumn([
                'url',
                'description_1',
                'description_2'
            ]);
        });
    }
}
