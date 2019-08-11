<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComputeInstanceDeviceCdRomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compute_instance_device_cd_roms', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('compute_instance_id');
            $table->tinyInteger('media_type')->nullable(); // Public iso or private iso
            $table->unsignedInteger('relative_media_id')->nullable();
            $table->tinyInteger('allow_detach')->default(0);
            $table->tinyInteger('allow_eject')->default(0);

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
        Schema::dropIfExists('c_d_r_o_ms');
    }
}
