<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublicIsosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('public_isos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id');
            $table->string('name', 32);
            $table->string('internal_name', 255);
            $table->smallInteger('order')->default(0);
            $table->tinyInteger('status')->default(\App\Constants\CommonStatusCode::AVAILABLE);
            $table->timestamps();

            $table
                ->foreign("category_id")
                ->references("id")
                ->on("public_iso_categories")
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
        Schema::dropIfExists('public_isos');
    }
}
