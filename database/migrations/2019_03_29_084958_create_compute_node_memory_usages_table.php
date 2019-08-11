<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComputeNodeMemoryUsagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compute_node_memory_usages', function (Blueprint $table) {
            $table->unsignedInteger('compute_node_id');
            $table->unsignedBigInteger('id');

            $table->double('total');
            $table->double('free');
            $table->double('available');

            $table->double('microtime');

            $table->primary(["compute_node_id", "id"], "compute_node_memory_usages_pk");
            $table->index("microtime");

            $table
                ->foreign("compute_node_id")
                ->references("id")
                ->on("compute_nodes")
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
        Schema::dropIfExists('compute_node_memory_usages');
    }
}
