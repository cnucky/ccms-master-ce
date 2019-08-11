<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_replies', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ticket_id');
            $table->unsignedInteger('admin_id')->nullable();
            $table->string('admin_name', 32)->nullable();
            $table->text('content');
            $table->timestamps();

            $table
                ->foreign("ticket_id")
                ->references("id")
                ->on("tickets")
                ->onUpdate("cascade")
                ->onDelete("cascade")
            ;

            $table
                ->foreign("admin_id")
                ->references("id")
                ->on("admins")
                ->onUpdate("cascade")
                ->onDelete("set null")
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
        Schema::dropIfExists('ticket_replies');
    }
}
