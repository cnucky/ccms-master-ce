<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentTradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_trades', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('no', 32)->unique();

            $table->unsignedInteger('payment_module_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();

            $table->decimal('fee_in_default_currency', 11, 2);
            $table->decimal('fee', 11, 2);
            $table->unsignedDecimal('refundable_fee', 11, 2)->default(0);

            $table->string('basic_payment_module', 255);
            $table->string('channel', 255)->nullable();

            $table->tinyInteger('status')->default(0);
            $table->string('transaction_id')->nullable();

            $table->string('remark', 255)->nullable();

            $table->text('payment_module_data')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table
                ->foreign("payment_module_id")
                ->references("id")
                ->on("payment_modules")
                ->onUpdate("cascade")
                ->onDelete("set null")
            ;

            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users")
                ->onUpdate("cascade")
                ->onDelete("set null")
            ;

            $table->index("transaction_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_trades');
    }
}
