<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCreditRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_credit_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->decimal('amount', 13, 4);
            $table->unsignedTinyInteger('type');
            $table->unsignedInteger('relative_object_id')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(["type", "user_id", "relative_object_id"]);

            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users")
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
        Schema::dropIfExists('user_credit_records');
    }
}
