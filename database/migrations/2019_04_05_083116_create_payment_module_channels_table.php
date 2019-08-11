<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentModuleChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_module_channels', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('payment_module_id');
            $table->string('channel_internal_name');
            $table->string('channel_display_name')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('order')->default(0);

            $table
                ->foreign("payment_module_id")
                ->references("id")
                ->on("payment_modules")
                ->onUpdate("cascade")
                ->onDelete("cascade")
            ;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_module_channels');
    }
}
