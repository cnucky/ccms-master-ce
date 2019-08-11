<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComputeNodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compute_nodes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('zone_id');
            $table->string('name', 32);
            $table->string('description')->nullable();

            $table->string('host', 128);
            $table->unsignedInteger('trusted_certificate_id')->nullable();
            $table->text('private_key')->nullable();
            $table->text('certificate')->nullable();

            $table->string('token')->nullable();

            $table->tinyInteger('status')->default(\App\Constants\StatusCode::STATUS_NORMAL);

            $table->string("cpu_model")->nullable();
            $table->unsignedTinyInteger("cpu_cores")->nullable();

            $table->unsignedInteger('uptime')->default(0);

            $table->double('cpu_utilization')->default(0);

            $table->double('total_disk_capacity_in_gib_unit')->default(0);
            $table->unsignedInteger('total_memory_capacity_in_mib_unit')->default(0);

            $table->unsignedInteger('total_allocated_disk_in_gib_unit')->default(0);
            $table->unsignedInteger('total_allocated_memory_in_mib_unit')->default(0);

            $table->double('current_disk_free_in_gib_unit')->default(0);
            $table->unsignedInteger('current_memory_free_in_mib_unit')->default(0);


            $table->unsignedInteger('reserved_memory_capacity_in_mib_unit')->nullable();
            $table->unsignedInteger('reserved_disk_capacity_in_gib_unit')->nullable();

            $table->unsignedInteger('max_instance')->nullable();

            $table->unsignedInteger('instance_counter')->default(0);
            $table->unsignedInteger('local_volume_counter')->default(0);

            $table->char('uuid', 36)->unique();

            $table->string('no_vnc_host', 255)->nullable();
            $table->unsignedSmallInteger('no_vnc_port')->nullable();
            $table->unsignedSmallInteger('no_vnc_client_connect_port')->nullable();

            $table->unsignedInteger('certificate_id')->nullable();

            $table->timestamp('last_communicated_at')->nullable();

            $table->timestamps();

            $table
                ->foreign("zone_id")
                ->references("id")
                ->on("zones")
                ->onUpdate("cascade")
                ->onDelete("restrict")
            ;

            $table
                ->foreign("trusted_certificate_id")
                ->references("id")
                ->on("trusted_certificates")
                ->onUpdate("cascade")
                ->onDelete("set null")
            ;

            $table
                ->foreign("certificate_id")
                ->references("id")
                ->on("certificates")
                ->onUpdate("cascade")
                ->onDelete("set null")
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
        Schema::dropIfExists('compute_nodes');
    }
}
