<?php

use App\Utils\AccountType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('account_type')->default(AccountType::BASIC);
            $table->string('device_token')->nullable();
            $table->string('credit')->nullable();
            $table->string('stripe_id')->nullable()->index();
            $table->string('card_brand')->nullable();
            $table->string('card_last_four', 4)->nullable();
            $table->timestamp('trial_ends_at')->nullable();
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
            $table->dropColumn([
                'account_type',
                'device_token',
                'credit',
                'stripe_id',
                'card_brand',
                'card_last_four',
                'trial_ends_at',
            ]);
        });
    }
}
