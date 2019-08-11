<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZoneHasComputeInstancePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zone_has_compute_instance_packages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('zone_id');
            $table->unsignedInteger('package_id');
            $table->unsignedInteger('stock')->nullable();

            $table
                ->foreign("zone_id")
                ->references("id")
                ->on("zones")
                ->onUpdate("cascade")
                ->onDelete("cascade")
            ;

            $table
                ->foreign("package_id")
                ->references("id")
                ->on("compute_instance_packages")
                ->onUpdate("cascade")
                ->onDelete("cascade")
            ;

            $table->unique(["zone_id", "package_id"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zone_has_compute_instance_packages');
    }
}
