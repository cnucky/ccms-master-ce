<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 32);
            $table->string('email', 64)->unique();
            $table->char('country', 2);
            $table->string('phone', 16);
            $table->string('password', 255);
            $table->tinyInteger('status');
            $table->decimal('credit', 13, 4)->default(0);
            $table->unsignedDecimal('frozen_credit', 13, 4)->default(0);
            $table->tinyInteger('timezone')->nullable();
            $table->string('lang', 8)->nullable();

            $table->tinyInteger('disable_quota_auto_upgrade')->default(0);
            $table->unsignedInteger('user_quota_id');

            $table->tinyInteger('inactive')->default(\App\Constants\UserInactiveStatusCodes::STATUS_NORMAL);

            $table->timestamp('start_lack_of_credit_at')->nullable();

            $table->rememberToken();

            \App\Utils\ChargeMigration::createChargeColumn($table, 'users');

            $table->timestamps();

            $table->index(["inactive", "credit", "start_lack_of_credit_at"]);

            $table
                ->foreign("user_quota_id")
                ->references("id")
                ->on("user_quotas")
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
        Schema::dropIfExists('users');
    }
}
