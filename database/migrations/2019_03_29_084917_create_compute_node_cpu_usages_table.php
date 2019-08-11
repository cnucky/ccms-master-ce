<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComputeNodeCpuUsagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compute_node_cpu_usages', function (Blueprint $table) {
            $table->unsignedInteger('compute_node_id');
            $table->unsignedBigInteger('id');
            $table->char('processor', 8);

            $table->double('user');
            $table->double('nice');
            $table->double('system');
            $table->double('idle');
            $table->double('iowait');
            $table->double('irq');
            $table->double('softirq');
            $table->double('steal');
            $table->double('guest');
            $table->double('guest_nice');

            $table->double('microtime');

            $table->primary(["compute_node_id", "id", "processor"], "compute_node_cpu_usages_pk");
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
        Schema::dropIfExists('compute_node_cpu_usages');
    }
}
