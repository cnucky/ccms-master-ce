<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZoneHasIPv4PoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zone_has_ipv4_pools', function (Blueprint $table) {
            $table->unsignedInteger('pool_id');
            $table->unsignedInteger('zone_id');
            $table->timestamps();

            $table
                ->foreign("pool_id")
                ->references("id")
                ->on("ipv4_pools")
                ->onUpdate("cascade")
                ->onDelete("cascade")
            ;

            $table
                ->foreign("zone_id")
                ->references("id")
                ->on("zones")
                ->onUpdate("cascade")
                ->onDelete("cascade")
            ;

            $table->primary(["pool_id", "zone_id"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zone_has_ipv4_pools');
    }
}
