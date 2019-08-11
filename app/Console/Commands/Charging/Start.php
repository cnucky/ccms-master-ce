<?php

namespace App\Console\Commands\Charging;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Start extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'charge:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start charging.';

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
        $this->call("charge:fix-owner");
        $this->call("charge:ci");
        $this->call("charge:lv");
        $this->call("charge:ip");
        $this->call("charge:user");
        $this->call("charge:fix-owner");
        return 0;
    }
}
