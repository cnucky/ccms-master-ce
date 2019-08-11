<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('department_id');
            $table->unsignedInteger('user_id');
            $table->tinyInteger('priority');
            $table->unsignedTinyInteger('status');
            $table->string('title', 255);
            $table->tinyInteger('product_type')->nullable();
            $table->unsignedBigInteger('relative_service_id')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();

            $table
                ->foreign("department_id")
                ->references("id")
                ->on("ticket_departments")
                ->onUpdate("cascade")
                ->onDelete("restrict")
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
        Schema::dropIfExists('tickets');
    }
}
