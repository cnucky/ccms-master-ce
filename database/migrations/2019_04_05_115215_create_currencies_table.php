<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->char('code', 3);
            $table->string('prefix', 8)->nullable();
            $table->string('suffix', 8)->nullable();
            $table->tinyInteger('format')->default(0);
            $table->decimal('exchange_rate', 12, 6);
        });

        \App\Currency::query()->insert([
            "id" => 1,
            "code" => "CNY",
            "prefix" => "￥",
            "exchange_rate" => "1",
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currencies');
    }
}
