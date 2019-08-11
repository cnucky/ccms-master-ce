<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComputeInstanceBandwidthUsagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compute_instance_bandwidth_usages', function (Blueprint $table) {
            $table->unsignedInteger('network_interface_id');
            $table->unsignedBigInteger('id');

            $table->double('rx_bytes_per_second');
            $table->double('rx_packets_per_second');
            $table->double('tx_bytes_per_second');
            $table->double('tx_packets_per_second');

            $table->double('microtime');

            $table->primary(["network_interface_id", "id"], "compute_instance_bandwidth_usages_pk");
            $table->index("microtime");

            $table
                ->foreign("network_interface_id")
                ->references("id")
                ->on("compute_instance_network_interfaces")
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
        Schema::dropIfExists('compute_instance_bandwidth_usages');
    }
}
