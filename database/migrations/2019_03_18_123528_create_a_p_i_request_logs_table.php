<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAPIRequestLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_request_logs', function (Blueprint $table) {
            $table->engine = "MyISAM";
            $table->bigIncrements('id');
            $table->string("url", 255)->nullable();
            $table->mediumText("raw_response")->nullable();
            $table->mediumText("log")->nullable();
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
        Schema::dropIfExists('api_request_logs');
    }
}
