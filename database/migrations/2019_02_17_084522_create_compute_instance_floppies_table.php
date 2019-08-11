<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComputeInstanceFloppiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compute_instance_floppies', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('compute_instance_id');
            $table->tinyInteger('media_type')->nullable();
            $table->string('relative_media_id')->nullable();
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
        Schema::dropIfExists('floppies');
    }
}
