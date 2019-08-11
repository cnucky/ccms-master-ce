<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrafficShareGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traffic_share_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 32)->unique();
            $table->string('description', 255)->nullable();
            $table->decimal('price_per_rx_gib', 8, 4)->default(0);
            $table->decimal('price_per_tx_gib', 8, 4)->default(0);
            $table->timestamps();
        });

        \App\TrafficShareGroup::query()->create([
            "id" => 1,
            "name" => "Default",
            "description" => "System created traffic share group",
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('traffic_share_groups');
    }
}
