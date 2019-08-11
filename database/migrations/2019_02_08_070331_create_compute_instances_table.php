<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComputeInstancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compute_instances', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unique_id', 36)->unique();
            $table->char('uuid', 36)->nullable();

            $table->unsignedInteger('user_id');
            $table->unsignedInteger("compute_node_id");
            $table->unsignedInteger("compute_instance_package_id")->nullable();

            $table->tinyInteger('type');

            $table->string('name', 32);
            $table->string('hostname', 64);
            $table->string('description', 255)->nullable();

            $table->unsignedInteger('override_vCPU')->nullable();
            $table->unsignedInteger('override_memory')->nullable();
            // $table->unsignedInteger('override_storage_size')->nullable();
            $table->integer('override_min_storage_capacity')->nullable();
            $table->integer('override_max_storage_capacity')->nullable();

            $table->unsignedInteger('override_public_ipv4')->nullable();
            $table->unsignedInteger('override_public_ipv4_block_size')->nullable();
            $table->unsignedInteger('override_public_ipv6')->nullable();
            $table->unsignedTinyInteger('override_public_ipv6_block_size')->nullable();

            $table->unsignedInteger('override_private_ipv4')->nullable();
            $table->unsignedInteger('override_private_ipv4_block_size')->nullable();
            $table->integer('override_max_elastic_ipv4_block')->nullable();

            $table->unsignedInteger('override_private_ipv6')->nullable();
            $table->unsignedTinyInteger('override_private_ipv6_block_size')->nullable();
            $table->integer('override_max_elastic_ipv6_block')->nullable();

            $table->integer('override_traffic')->nullable();
            $table->integer('override_inbound_traffic')->nullable();
            $table->integer('override_outbound_traffic')->nullable();
            $table->unsignedInteger('override_inbound_bandwidth')->nullable();
            $table->unsignedInteger('override_outbound_bandwidth')->nullable();
            $table->unsignedSmallInteger('override_io_weight')->nullable();
            $table->unsignedInteger('override_read_bytes_sec')->nullable();
            $table->unsignedInteger('override_write_bytes_sec')->nullable();
            $table->unsignedInteger('override_read_iops_sec')->nullable();
            $table->unsignedInteger('override_write_iops_sec')->nullable();

            $table->tinyInteger('machine_type')->default(0);
            $table->tinyInteger('use_legacy_bios')->default(0);
            $table->tinyInteger('no_clean_traffic')->default(0);

            $table->string('password', 255)->nullable();
            $table->string('vnc_password', 8)->nullable();

            $table->tinyInteger('status');

            $table->tinyInteger('power_status')->nullable();

            $table->decimal('override_price_per_hour', 8, 4)->nullable();

            $table->double('twenty_minutes_average_cpu_utilization')->default(0);
            $table->double('twenty_minutes_average_public_network_rx_bytes_per_second')->default(0);
            $table->double('twenty_minutes_average_public_network_tx_bytes_per_second')->default(0);
            $table->double('twenty_minutes_average_private_network_rx_bytes_per_second')->default(0);
            $table->double('twenty_minutes_average_private_network_tx_bytes_per_second')->default(0);
            $table->double('twenty_minutes_average_disk_read_bytes_per_second')->default(0);
            $table->double('twenty_minutes_average_disk_write_bytes_per_second')->default(0);
            $table->double('twenty_minutes_average_disk_read_req_per_second')->default(0);
            $table->double('twenty_minutes_average_disk_write_req_per_second')->default(0);


            \App\Utils\ChargeMigration::createChargeColumn($table, "compute_instances");

            $table->timestamps();
            $table->softDeletes();

            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users")
                ->onUpdate("cascade")
                ->onDelete("cascade")
            ;

            $table
                ->foreign("compute_node_id")
                ->references("id")
                ->on("compute_nodes")
                ->onUpdate("cascade")
                ->onDelete("cascade")
            ;

            $table
                ->foreign("compute_instance_package_id")
                ->references("id")
                ->on("compute_instance_packages")
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
        Schema::dropIfExists('compute_instances');
    }
}
