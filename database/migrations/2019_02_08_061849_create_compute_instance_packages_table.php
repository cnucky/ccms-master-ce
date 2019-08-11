<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComputeInstancePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compute_instance_packages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 32);

            $table->unsignedInteger('category_id');

            $table->unsignedInteger('vCPU');
            $table->unsignedInteger('memory');
            // $table->unsignedInteger('storage_size');

            $table->unsignedInteger('min_storage_capacity')->nullable();
            $table->unsignedInteger('max_storage_capacity')->nullable();

            $table->unsignedInteger('public_ipv4')->default(0);
            $table->unsignedTinyInteger('public_ipv4_block_size')->nullable();
            $table->unsignedInteger('max_elastic_ipv4_block')->nullable();

            $table->unsignedInteger('public_ipv6')->default(0);
            $table->unsignedTinyInteger('public_ipv6_block_size')->nullable();
            $table->unsignedInteger('max_elastic_ipv6_block')->nullable();


            $table->unsignedInteger('private_ipv4')->default(0);
            $table->unsignedTinyInteger('private_ipv4_block_size')->nullable();
            $table->unsignedInteger('private_ipv6')->default(0);
            $table->unsignedTinyInteger('private_ipv6_block_size')->nullable();

            $table->integer('traffic')->nullable();
            $table->integer('inbound_traffic')->nullable();
            $table->integer('outbound_traffic')->nullable();
            $table->unsignedInteger('inbound_bandwidth')->default(0);
            $table->unsignedInteger('outbound_bandwidth')->default(0);
            $table->unsignedSmallInteger('io_weight')->nullable();
            $table->unsignedInteger('read_bytes_sec')->default(0);
            $table->unsignedInteger('write_bytes_sec')->default(0);
            $table->unsignedInteger('read_iops_sec')->default(0);
            $table->unsignedInteger('write_iops_sec')->default(0);

            $table->smallInteger('order')->default(0);
            $table->tinyInteger('status')->default(0);

            $table->decimal('price_per_hour', 8, 4);

            $table->timestamps();

            $table
                ->foreign("category_id")
                ->references("id")
                ->on("compute_instance_package_categories")
                ->onUpdate("cascade")
                ->onDelete("cascade")
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
        Schema::dropIfExists('packages');
    }
}
