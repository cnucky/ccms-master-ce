<?php

namespace App\Console\Commands\User\Inactive;

use App\Constants\UserInactiveStatusCodes;
use App\User;
use Illuminate\Console\Command;

class Clear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:inactive:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear user\'s inactive.';

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
        $updateRecordCount = User::query()
            ->where("credit", ">=", "0")
            ->where("inactive", "!=", UserInactiveStatusCodes::STATUS_NORMAL)
            ->update([
                "start_lack_of_credit_at" => null,
                "inactive" => UserInactiveStatusCodes::STATUS_NORMAL,
            ])
        ;
        $this->info("$updateRecordCount user(s) updated.");
        return 0;
    }
}
