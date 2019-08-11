<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComputeNodePublicImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compute_node_public_images', function (Blueprint $table) {
            $table->unsignedInteger('compute_node_id');
            $table->string('internal_name');
            $table->string('version', 16);

            $table->primary(["compute_node_id", "internal_name", "version"], "compute_node_public_images_pk");

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
        Schema::dropIfExists('compute_node_images');
    }
}
