<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserQuotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_quotas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 32)->unique();
            $table->string('description', 255)->nullable();
            $table->unsignedInteger('max_instance')->nullable();
            $table->unsignedInteger('max_storage_capacity_in_gib_unit')->nullable();

            $table->unsignedInteger('max_elastic_ipv4_block')->nullable();
            $table->unsignedInteger('max_elastic_ipv6_block')->nullable();

            $table->unsignedInteger('max_monthly_public_network_rx_traffic_in_gib_unit')->nullable();
            $table->unsignedInteger('max_monthly_public_network_tx_traffic_in_gib_unit')->nullable();

            $table->unsignedInteger('max_monthly_private_network_rx_traffic_in_gib_unit')->nullable();
            $table->unsignedInteger('max_monthly_private_network_tx_traffic_in_gib_unit')->nullable();

            $table->unsignedInteger('auto_upgrade_after_net_income_more_than')->nullable();

            $table->timestamps();
        });

        \App\UserQuota::query()->create([
            "id" => 1,
            "name" => "Default",
            "description" => "System created default user quota",
            "auto_upgrade_after_net_income_more_than" => 0,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_quotas');
    }
}
