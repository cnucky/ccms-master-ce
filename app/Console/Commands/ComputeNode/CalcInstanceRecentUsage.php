<?php

namespace App\Console\Commands\ComputeNode;

use App\ComputeInstance;
use Illuminate\Console\Command;

class CalcInstanceRecentUsage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cn:ci:calc-instance-recent-usage {nodeId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate instance recent usage';

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
        ComputeInstance::calcInstanceRecent20MinutesUsage($this->argument("nodeId"));
        return 0;
    }
}
