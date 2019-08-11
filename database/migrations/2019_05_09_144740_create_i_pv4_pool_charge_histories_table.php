<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIPv4PoolChargeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('i_pv4_pool_charge_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('pool_id')->nullable();
            $table->unsignedDecimal('amount', 13, 4);

            $table->timestamp('created_at')->nullable();

            $table
                ->foreign("pool_id")
                ->references("id")
                ->on("ipv4_pools")
                ->onUpdate("cascade")
                ->onDelete("set null")
            ;

            $table->index([
                "pool_id",
                "created_at",
            ], "i_pv4_pool_charge_histories_index");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('i_pv4_pool_charge_histories');
    }
}
