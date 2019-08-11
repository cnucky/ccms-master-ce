<?php

namespace App\Console\Commands\User;

use Illuminate\Console\Command;

class Inactive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Maintaining user\'s inactive tag.';

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
        $this->call("user:inactive:clear");
        $this->call("user:inactive:prepare");
        $this->call("user:inactive:set");
        return 0;
    }
}
