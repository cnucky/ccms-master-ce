<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComputeInstanceLocalVolumesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compute_instance_local_volumes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unique_id', 36)->unique();

            $table->unsignedInteger('user_id');

            $table->unsignedInteger('compute_node_id');

            $table->string('description', 255)->nullable();

            $table->unsignedInteger('capacity');
            $table->unsignedInteger('image_id')->nullable();
            $table->unsignedTinyInteger('attach_order')->default(255);
            $table->tinyInteger('bus')->default(0); // virtio, sata, ide

            $table->tinyInteger('allow_detach')->default(0);

            $table->unsignedInteger('attached_compute_instance_id')->nullable();

            $table->decimal('override_price_per_hour', 8, 4)->nullable();

            $table->tinyInteger('protected')->default(0);

            $table->tinyInteger('status')->nullable();

            \App\Utils\ChargeMigration::createChargeColumn($table, "compute_instance_local_volumes");

            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade')
            ;

            $table->foreign('compute_node_id')
                ->references('id')
                ->on('compute_nodes')
                ->onDelete('cascade')
                ->onUpdate('cascade')
            ;

            $table->foreign('attached_compute_instance_id', "attached_compute_instance_fk")
                ->references('id')
                ->on('compute_instances')
                ->onDelete('cascade')
                ->onUpdate('cascade')
            ;

            $table->foreign('image_id')
                ->references('id')
                ->on('images')
                ->onDelete('set null')
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
        Schema::dropIfExists('compute_instance_volumes');
    }
}
