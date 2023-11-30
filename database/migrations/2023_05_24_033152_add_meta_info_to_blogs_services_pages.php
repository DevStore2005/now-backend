<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMetaInfoToBlogsServicesPages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('og_title')->nullable()->after('views');
            $table->text('og_description')->nullable()->after('og_title');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->string('og_title')->nullable()->after('type');
            $table->text('og_description')->nullable()->after('og_title');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->string('og_title')->nullable()->after('status');
            $table->text('og_description')->nullable()->after('og_title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn(['og_title', 'og_description']);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['og_title', 'og_description']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['og_title', 'og_description']);
        });
    }
}
