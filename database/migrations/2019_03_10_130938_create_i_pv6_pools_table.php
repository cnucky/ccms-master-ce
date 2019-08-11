<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIPv6PoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ipv6_pools', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description', 255)->nullable();

            $table->unsignedInteger('first_usable_ip_part_0');
            $table->unsignedInteger('first_usable_ip_part_1');
            $table->unsignedInteger('first_usable_ip_part_2');
            $table->unsignedInteger('first_usable_ip_part_3');
            $table->string('human_readable_first_usable_ip', 45);

            $table->unsignedInteger('last_usable_ip_part_0');
            $table->unsignedInteger('last_usable_ip_part_1');
            $table->unsignedInteger('last_usable_ip_part_2');
            $table->unsignedInteger('last_usable_ip_part_3');
            $table->string('human_readable_last_usable_ip', 45);

            $table->unsignedInteger('network_bits');

            $table->unsignedInteger('gateway_part_0')->nullable();
            $table->unsignedInteger('gateway_part_1')->nullable();
            $table->unsignedInteger('gateway_part_2')->nullable();
            $table->unsignedInteger('gateway_part_3')->nullable();
            $table->string('human_readable_gateway', 45)->nullable();

            $table->unsignedTinyInteger('subnet_network_bits')->default(128);
            $table->unsignedInteger('total_subnet');

            $table->tinyInteger('assign_for_new_instance');
            $table->tinyInteger('assign_for_extra_ip');

            $table->decimal('price_per_hour', 8, 4)->default(0);

            $table->tinyInteger('type');
            $table->tinyInteger('status')->default(\App\Constants\CommonStatusCode::AVAILABLE);

            $table->timestamps();

            $table->index([
                "first_usable_ip_part_0", "first_usable_ip_part_1", "first_usable_ip_part_2", "first_usable_ip_part_3",
                "last_usable_ip_part_0", "last_usable_ip_part_1", "last_usable_ip_part_2", "last_usable_ip_part_3",
                ], "ipv6_pools_usable_ip_index");
            $table->index(["human_readable_first_usable_ip", "human_readable_last_usable_ip"], "ipv6_pool_human_readable_ip_index");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ipv6_pools');
    }
}
