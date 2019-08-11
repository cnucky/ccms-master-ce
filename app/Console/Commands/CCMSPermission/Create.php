<?php

namespace App\Console\Commands\CCMSPermission;

use App\Constants\AdminPermissions;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class Create extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ccms:permission:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create CCMS permissions.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $reflectionClass = new \ReflectionClass(AdminPermissions::class);
        $permissions = $reflectionClass->getConstants();

        Permission::query()
            ->where("guard_name", "admin")
            ->whereNotIn("name", $permissions)
            ->delete()
        ;

        foreach ($permissions as $key => $value) {
            $this->info(sprintf("%s => %s,", $key, $value));
            Permission::query()->firstOrCreate([
                "name" => $value,
                "guard_name" => "admin",
            ]);
        }
        return 0;
    }
}
