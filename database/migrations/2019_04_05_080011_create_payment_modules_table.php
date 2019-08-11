<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_modules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('basic_payment_module', 255);
            $table->string('name', 32)->unique();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('currency_id')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->smallInteger('order')->default(0);
            $table->timestamps();

            $table->index(["order", "status"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_modules');
    }
}
