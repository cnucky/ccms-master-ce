<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrafficShareGroupChargeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traffic_share_group_charge_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('traffic_share_group_id')->nullable();
            $table->tinyInteger('type');
            $table->unsignedDecimal('amount', 13, 4);

            $table->timestamp('created_at')->nullable();

            $table
                ->foreign("traffic_share_group_id", "traffic_share_group_charge_histories_fk")
                ->references("id")
                ->on("traffic_share_groups")
                ->onUpdate("cascade")
                ->onDelete("set null")
            ;

            $table->index([
                "traffic_share_group_id",
                "created_at",
                "type",
            ], "traffic_share_group_charge_histories_index");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('traffic_share_group_charge_histories');
    }
}
