<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('region_id');
            $table->string('name', 32);
            $table->string('description', 255)->nullable();
            $table->tinyInteger('status')->default(\App\Constants\StatusCode::STATUS_NORMAL);

            $table->decimal('storage_price_per_hour_per_gib', 8, 4)->default(0);

            $table->unsignedInteger('traffic_share_group_id');

            $table->timestamps();

            $table
                ->foreign("region_id")
                ->references("id")
                ->on("regions")
                ->onUpdate("cascade")
                ->onDelete("restrict")
            ;

            $table
                ->foreign("traffic_share_group_id")
                ->references("id")
                ->on("traffic_share_groups")
                ->onUpdate("cascade")
                ->onDelete("restrict")
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
        Schema::dropIfExists('zones');
    }
}
