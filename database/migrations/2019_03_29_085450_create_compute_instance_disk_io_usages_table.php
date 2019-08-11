<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComputeInstanceDiskIoUsagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compute_instance_disk_io_usages', function (Blueprint $table) {
            $table->unsignedInteger('compute_instance_id');
            $table->unsignedBigInteger('id');

            $table->double('rd_req_per_second');
            $table->double('rd_bytes_per_second');
            $table->double('wr_req_per_second');
            $table->double('wr_bytes_per_second');

            $table->double('microtime');

            $table->primary(["compute_instance_id", "id"], "compute_instance_disk_io_usages_pk");
            $table->index("microtime");


            $table
                ->foreign("compute_instance_id")
                ->references("id")
                ->on("compute_instances")
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
        Schema::dropIfExists('compute_instance_disk_io_usages');
    }
}
