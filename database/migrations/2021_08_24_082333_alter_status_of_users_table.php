<?php

use Doctrine\DBAL\Types\Type;
use Illuminate\Support\Facades\DB;
use Doctrine\DBAL\Types\StringType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStatusOfUsersTable extends Migration
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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['ACTIVE', 'PENDING', 'DISABLED', 'SUSPENDED'])->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['ACTIVE', 'PENDING', 'DISABLED'])->change();
        });
    }
}
