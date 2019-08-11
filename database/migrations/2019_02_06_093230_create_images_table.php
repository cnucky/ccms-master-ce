<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('image_category_id');
            $table->string('name', 32);
            $table->string('internal_name', 255);
            $table->string('force_version', 16)->nullable();

            $table->unsignedInteger('min_disk')->default(20);
            $table->unsignedInteger('min_memory')->default(0);
            $table->tinyInteger('machine_type')->default(\YunInternet\CCMSCommon\Constants\MachineType::TYPE_Q35);
            $table->tinyInteger('use_legacy_bios')->default(0);

            $table->smallInteger('order')->default(0);
            $table->tinyInteger('status')->default(\App\Constants\CommonStatusCode::AVAILABLE);
            $table->decimal('price_per_hour', 8, 4)->default(0);
            $table->timestamps();

            $table
                ->foreign("image_category_id")
                ->references("id")
                ->on("image_categories")
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
        Schema::dropIfExists('images');
    }
}
