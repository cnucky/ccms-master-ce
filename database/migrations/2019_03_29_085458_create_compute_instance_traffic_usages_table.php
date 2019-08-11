<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComputeInstanceTrafficUsagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compute_instance_traffic_usages', function (Blueprint $table) {
            $table->unsignedInteger('network_interface_id');
            $table->unsignedBigInteger('id');

            $table->unsignedInteger('user_id');
            $table->unsignedInteger('network_type');
            $table->unsignedInteger('traffic_share_group_id')->nullable();

            $table->bigInteger('rx_byte_count');
            $table->bigInteger('rx_packet_count');
            $table->bigInteger('tx_byte_count');
            $table->bigInteger('tx_packet_count');

            \App\Utils\ChargeMigration::createChargeColumn($table, "compute_instance_traffic_usages");

            $table->double('microtime');

            $table->primary(["network_interface_id", "id"], "compute_instance_traffic_usages_pk");
            $table->index(["microtime", "user_id", "network_type", "traffic_share_group_id"], "compute_instance_traffic_usages_time_user_type_index");

            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users")
                ->onUpdate("cascade")
                ->onDelete("cascade")
            ;

            $table
                ->foreign("traffic_share_group_id")
                ->references("id")
                ->on("traffic_share_groups")
                ->onUpdate("cascade")
                ->onDelete("restrict")
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
        Schema::dropIfExists('compute_instance_traffic_usages');
    }
}
