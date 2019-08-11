<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImageNodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('image_nodes', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('zone_id');
            $table->string('name', 32);
            $table->string('description')->nullable();

            $table->string('host', 128);

            $table->string('host_inside_zone', 128)->nullable();

            $table->unsignedInteger('trusted_certificate_id')->nullable();
            $table->text('private_key')->nullable();
            $table->text('certificate')->nullable();

            $table->string('public_image_sync_password');
            $table->string('private_image_sync_password');

            $table->string('token')->nullable();

            $table->tinyInteger('zone_default_image_node')->default(0);

            $table->tinyInteger('status')->nullable();

            $table->timestamps();

            $table
                ->foreign("zone_id")
                ->references("id")
                ->on("zones")
                ->onUpdate("cascade")
                ->onDelete("cascade")
            ;

            $table
                ->foreign("trusted_certificate_id")
                ->references("id")
                ->on("trusted_certificates")
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
        Schema::dropIfExists('image_nodes');
    }
}
