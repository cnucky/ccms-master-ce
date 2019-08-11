<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InitPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $reflectionClass = new \ReflectionClass(\App\Constants\AdminPermissions::class);
        foreach ($reflectionClass->getConstants() as $key => $value) {
            \Spatie\Permission\Models\Permission::query()->firstOrCreate([
                "name" => $value,
                "guard_name" => "admin",
            ]);
        }

        /**
         * @var \Spatie\Permission\Models\Role $role
         */
        $role = App\Role::query()->firstOrCreate([
            "name" => "Administrators",
            "guard_name" => "admin",
        ]);

        $role->givePermissionTo(\App\Constants\AdminPermissions::SUPER);

        /**
         * @var \App\Admin $admin
         */
        try {
            $admin = \App\Admin::query()->findOrFail(1);
        } catch (Exception $e) {
            $admin = \App\Admin::query()->create([
                "id" => 1,
                "name" => "Administrator",
                "email" => "root@ccms.localhost",
                "password" => bcrypt($password = base64_encode(\random_bytes(8))),
                "status" => \App\Constants\StatusCode::STATUS_NORMAL,
            ]);
            echo "Email: root@ccms.localhost, password: $password" . PHP_EOL;
        }

        $admin->assignRole("Administrators");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
