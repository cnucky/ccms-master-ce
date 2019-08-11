<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIPv4AssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ipv4_assignments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('pool_id');
            $table->unsignedInteger('position');
            $table->string('human_readable_first_usable', 15);
            $table->string('human_readable_last_usable', 15);
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('nic_id')->nullable();
            $table->decimal('override_price_per_hour', 8, 4)->nullable();

            $table->tinyInteger('unbindable')->default(1);
            $table->tinyInteger('status')->default(0);
            $table->timestamp('assigned_at')->nullable();

            \App\Utils\ChargeMigration::createChargeColumn($table, "ipv4_assignments");

            $table->timestamps();

            $table
                ->foreign("pool_id")
                ->references("id")
                ->on("ipv4_pools")
                ->onUpdate("cascade")
                ->onDelete("cascade")
            ;

            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users")
                ->onUpdate("cascade")
                ->onDelete("set null")
            ;

            $table
                ->foreign("nic_id")
                ->references("id")
                ->on("compute_instance_network_interfaces")
                ->onUpdate("cascade")
                ->onDelete("set null")
            ;

            $table->unique(["pool_id", "position"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ipv4_assignments');
    }
}
