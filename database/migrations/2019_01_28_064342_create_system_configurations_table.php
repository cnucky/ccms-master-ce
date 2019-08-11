<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSystemConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_configurations', function (Blueprint $table) {
            $table->string('name', 64);
            $table->text('value')->nullable();
            $table->timestamps();

            $table->primary('name');
        });

        $microTime = base_convert(intval(microtime(true) * 10000), 10, 36);
        $random = base_convert(mt_rand(1000000000, 2000000000), 10, 36);


        \App\SystemConfiguration::setValue(\App\Constants\AvailableSystemConfigurations::SYSTEM_ID, $microTime . $random);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_configurations');
    }
}
