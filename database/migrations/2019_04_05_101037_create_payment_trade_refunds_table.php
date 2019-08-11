<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentTradeRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_trade_refunds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('trade_id');
            $table->string('no', 32)->unique();

            $table->decimal('fee_in_default_currency', 11, 2);
            $table->decimal('fee', 11, 2);
            $table->string('transaction_id')->nullable();

            $table->tinyInteger('status')->default(0);

            $table->string('remark', 255)->nullable();

            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();

            $table
                ->foreign("trade_id")
                ->references("id")
                ->on("payment_trades")
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
        Schema::dropIfExists('payment_trade_refunds');
    }
}
