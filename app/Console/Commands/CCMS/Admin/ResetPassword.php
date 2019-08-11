<?php

namespace App\Console\Commands\CCMS\Admin;

use App\Admin;
use Illuminate\Console\Command;

class ResetPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ccms:admin:reset-password {adminIdOrEmail}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset admin password.';

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
        $adminIdOrEmail = $this->argument("adminIdOrEmail");
        $queryBuilder = Admin::query();
        if (is_numeric($adminIdOrEmail)) {
            $queryBuilder->where("id", $adminIdOrEmail);
        } else {
            $queryBuilder->where("email", $adminIdOrEmail);
        }
        $admin = $queryBuilder->firstOrFail();
        $admin->update([
            "password" => bcrypt($newPassword = base64_encode(random_bytes(8))),
            "remember_token" => null,
        ]);
        $this->info("New password: " . $newPassword);
        return 0;
    }
}
