<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentModuleLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_module_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('payment_module_id')->nullable();
            $table->string('identify')->nullable();
            $table->tinyInteger('level')->nullable();
            $table->smallInteger('type')->nullable();
            $table->mediumText('log')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(["payment_module_id", "identify", "type", "level"]);

            $table
                ->foreign("payment_module_id")
                ->references("id")
                ->on("payment_modules")
                ->onUpdate("cascade")
                ->onDelete("set null")
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
        Schema::dropIfExists('payment_module_logs');
    }
}
