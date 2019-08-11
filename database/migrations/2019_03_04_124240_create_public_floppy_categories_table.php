<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublicFloppyCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('public_floppy_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 32);
            $table->string('icon_class', 16)->nullable();
            $table->smallInteger('order')->default(0);
            $table->tinyInteger('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('public_floppy_categories');
    }
}
