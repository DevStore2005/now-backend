<?php

use App\Utils\AppConst;
use Doctrine\DBAL\Types\Type;
use Illuminate\Support\Facades\DB;
use Doctrine\DBAL\Types\StringType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ArlerStatusOfServiceRequestsTable extends Migration
{
    /**
     * default constructor
     */
    public function __construct()
    {
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        Type::hasType('enum') ? Type::hasType('enum') : Type::addType('enum', StringType::class);
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->enum('status', [AppConst::PENDING, AppConst::ACCEPTED, AppConst::REJECTED, AppConst::CANCEL])->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->enum('status', [AppConst::PENDING, AppConst::ACCEPTED, AppConst::REJECTED])->change();
        });
    }
}
