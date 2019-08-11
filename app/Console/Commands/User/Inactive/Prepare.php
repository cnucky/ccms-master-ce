<?php

namespace App\Console\Commands\User\Inactive;

use App\Constants\UserInactiveStatusCodes;
use App\User;
use Illuminate\Console\Command;

class Prepare extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:inactive:prepare';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find user which lack of credit, and wait for releasing theirs resources.';

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
        $updatedRecords = User::query()
            ->where("credit", "<", "0")
            ->where("inactive", UserInactiveStatusCodes::STATUS_NORMAL)
            ->update([
                "inactive" => UserInactiveStatusCodes::STATUS_PENDING,
                "start_lack_of_credit_at" => date("Y-m-d H:i:s"),
            ])
        ;
        $this->info("$updatedRecords user(s) prepare inactive.");
        return 0;
    }
}
