<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentModuleSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_module_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('payment_module_id');
            $table->string('name');
            $table->text('value')->nullable();

            $table
                ->foreign("payment_module_id")
                ->references("id")
                ->on("payment_modules")
                ->onUpdate("cascade")
                ->onDelete("cascade")
            ;

            $table->unique(["payment_module_id", "name"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_module_settings');
    }
}
