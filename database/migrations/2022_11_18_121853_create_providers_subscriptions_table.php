<?php

use App\Utils\AppConst;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProvidersSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('providers_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('plan_id');
            $table->string('type')->nullable();
            $table->unsignedTinyInteger('duration')->nullable();
            $table->unsignedTinyInteger('off')->nullable();
            $table->date('start_date')->default(now());
            $table->date('end_date')->nullable();
            $table->string('status')->default(AppConst::ACTIVE);
            $table->timestamps();
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
        Schema::dropIfExists('providers_subscriptions');
    }
}
