<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComputeResourceOperationRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compute_resource_operation_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedSmallInteger('resource_type');
            $table->unsignedInteger('resource_id');
            $table->unsignedTinyInteger('type'); // Operation type
            $table->longText('data')->nullable();
            $table->longText('sensitive_data')->nullable();
            $table->unsignedTinyInteger('progress')->nullable();
            $table->tinyInteger('operation_status');
            $table->integer('sub_status')->nullable();

            $table->unsignedInteger('is_processed')->nullable();

            $table->text('details')->nullable();

            $table->timestamps();

            $table->index('user_id');
            $table->index(['resource_type', 'resource_id', 'operation_status'], "compute_resource_operation_requests_relation_index");

            // Prevent parallel operation
            $table->unique(['resource_type', 'resource_id', 'is_processed'], "compute_resource_operation_requests_prevent_parallel");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compute_resource_operation_requests');
    }
}
