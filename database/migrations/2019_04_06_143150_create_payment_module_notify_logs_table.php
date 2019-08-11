<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentModuleNotifyLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_module_notify_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('type')->nullable();
            $table->string('remote_address', 45)->nullable();
            $table->string('trade_no', 32)->nullable();
            $table->text('request_url')->nullable();
            $table->text('request_headers')->nullable();
            $table->text('raw_request_body')->nullable();
            $table->tinyInteger('validate_result')->nullable();
            $table->timestamps();

            $table->index(["trade_no", "type"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_module_notify_logs');
    }
}
