<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComputeInstancePackageChargeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compute_instance_package_charge_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('compute_instance_package_id')->nullable();
            $table->unsignedDecimal('amount', 13, 4);

            $table->timestamp('created_at')->nullable();

            $table
                ->foreign("compute_instance_package_id", "compute_instance_package_charge_histories_fk")
                ->references("id")
                ->on("compute_instance_packages")
                ->onUpdate("cascade")
                ->onDelete("set null")
            ;

            $table->index([
                "compute_instance_package_id",
                "created_at",
            ], "compute_instance_package_charge_histories_index");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compute_instance_package_charge_histories');
    }
}
