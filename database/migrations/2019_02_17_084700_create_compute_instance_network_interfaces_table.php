<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComputeInstanceNetworkInterfacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compute_instance_network_interfaces', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('compute_instance_id');
            $table->string('description', 255)->nullable();
            $table->tinyInteger('type')->default(0); // public network, private network
            $table->char('mac_address', 17)->nullable();
            $table->tinyInteger('model')->default(0); // virtio

            $table->tinyInteger('status')->nullable();

            $table->timestamps();

            $table->foreign('compute_instance_id')
                ->references('id')
                ->on('compute_instances')
                ->onDelete('cascade')
                ->onUpdate('cascade')
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
        Schema::dropIfExists('network_interfaces');
    }
}
