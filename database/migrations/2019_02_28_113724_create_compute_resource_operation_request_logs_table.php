<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComputeResourceOperationRequestLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compute_resource_operation_request_logs', function (Blueprint $table) {
            $table->engine = "MyISAM";

            $table->bigIncrements('id');
            $table->unsignedInteger('operation_request_id')->nullable();
            $table->tinyInteger('log_level')->nullable();
            $table->text('log')->nullable();
            $table->timestamps();

            $table
                ->foreign("operation_request_id", "cr_operation_log_request_id_fk")
                ->references("id")
                ->on("compute_resource_operation_requests")
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
        Schema::dropIfExists('compute_resource_operation_request_logs');
    }
}
