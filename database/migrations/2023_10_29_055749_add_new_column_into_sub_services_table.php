<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sub_services', function (Blueprint $table) {
            $table->tinyInteger('show_in_the_footer')->nullable()->default(0)->after('view_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_services', function (Blueprint $table) {
            $table->dropColumn('show_in_the_footer');
        });
    }
};
