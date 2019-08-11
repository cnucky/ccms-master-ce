<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComputeNodeDiskIoUsagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compute_node_disk_io_usages', function (Blueprint $table) {
            $table->unsignedInteger('compute_node_id');
            $table->unsignedBigInteger('id');
            $table->string('block_device');

            $table->double('read_bytes_per_second');
            $table->double('write_bytes_per_second');

            $table->double('microtime');

            $table->primary(["compute_node_id", "id", "block_device"], "compute_node_disk_io_usages_pk");
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
        Schema::dropIfExists('compute_node_disk_io_usages');
    }
}
