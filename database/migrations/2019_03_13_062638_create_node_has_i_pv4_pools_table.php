<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNodeHasIPv4PoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('node_has_ipv4_pools', function (Blueprint $table) {
            $table->unsignedInteger('pool_id');
            $table->unsignedInteger('node_id');
            $table->timestamps();

            $table
                ->foreign("pool_id")
                ->references("id")
                ->on("ipv4_pools")
                ->onUpdate("cascade")
                ->onDelete("cascade")
            ;

            $table
                ->foreign("node_id")
                ->references("id")
                ->on("compute_nodes")
                ->onUpdate("cascade")
                ->onDelete("cascade")
            ;

            $table->primary(["pool_id", "node_id"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('node_has_ipv4_pools');
    }
}
