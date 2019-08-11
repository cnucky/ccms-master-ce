<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComputeNodeLoadAveragesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compute_node_load_averages', function (Blueprint $table) {
            $table->unsignedInteger('compute_node_id');
            $table->unsignedBigInteger('id');

            $table->float('one_minute_average');
            $table->float('five_minutes_average');
            $table->float('fifteen_minutes_average');

            $table->double('microtime');

            $table->primary(["compute_node_id", "id"], "compute_node_load_averages_pk");
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
        Schema::dropIfExists('compute_node_load_averages');
    }
}
