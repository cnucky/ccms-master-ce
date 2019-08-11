<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComputeNodeChargeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compute_node_charge_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('compute_node_id')->nullable();
            $table->tinyInteger('type');
            $table->unsignedDecimal('amount', 13, 4);

            $table->timestamp('created_at')->nullable();

            $table
                ->foreign("compute_node_id")
                ->references("id")
                ->on("compute_nodes")
                ->onUpdate("cascade")
                ->onDelete("set null")
            ;

            $table->index([
                "compute_node_id",
                "created_at",
                "type",
            ], "compute_node_charge_histories_index");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compute_node_charge_histories');
    }
}
