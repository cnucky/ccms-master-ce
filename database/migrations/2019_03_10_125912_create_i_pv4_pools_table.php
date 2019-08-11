<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIPv4PoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ipv4_pools', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description', 255)->nullable();

            $table->unsignedInteger('first_usable_ip');
            $table->string('human_readable_first_usable_ip', 15);
            $table->unsignedInteger('last_usable_ip');
            $table->string('human_readable_last_usable_ip', 15);

            $table->unsignedTinyInteger('network_bits');

            $table->unsignedInteger('gateway')->nullable();
            $table->string('human_readable_gateway', 15)->nullable();

            $table->unsignedTinyInteger('subnet_network_bits')->default(32);
            $table->unsignedInteger('total_subnet');

            $table->tinyInteger('assign_for_new_instance');
            $table->tinyInteger('assign_for_extra_ip');

            $table->decimal('price_per_hour', 8, 4)->default(0);

            $table->tinyInteger('type');
            $table->tinyInteger('status')->default(\App\Constants\CommonStatusCode::AVAILABLE);

            $table->timestamps();

            $table->index(["first_usable_ip", "last_usable_ip"], "ipv4_pool_usable_ip_index");
            $table->index(["human_readable_first_usable_ip", "human_readable_last_usable_ip"], "ipv4_pool_human_readable_ip_index");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ipv4_pools');
    }
}
