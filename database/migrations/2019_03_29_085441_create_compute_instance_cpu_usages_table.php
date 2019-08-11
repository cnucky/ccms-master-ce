<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComputeInstanceCpuUsagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compute_instance_cpu_usages', function (Blueprint $table) {
            $table->unsignedInteger('compute_instance_id');
            $table->unsignedBigInteger('id');

            $table->double('cpu_usage');
            $table->double('user_usage');
            $table->double('system_usage');

            $table->double('microtime');

            $table->primary(["compute_instance_id", "id"], "compute_instance_cpu_usages_pk");
            $table->index("microtime");


            $table
                ->foreign("compute_instance_id")
                ->references("id")
                ->on("compute_instances")
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
        Schema::dropIfExists('compute_instance_cpu_usages');
    }
}
