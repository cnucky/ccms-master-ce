<?php

namespace App\Console\Commands\Charging;

use App\ComputeInstance\TrafficUsage;
use App\IPPool\IPv4Assignment;
use App\IPPool\IPv6Assignment;
use Illuminate\Console\Command;

class FixOwner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'charge:fix-owner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix owner';

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
        \App\ComputeInstance::fixChargingByNonExistsOwner();
        \App\ComputeInstance\LocalVolume::fixChargingByNonExistsOwner();
        IPv4Assignment::fixChargingByNonExistsOwner();
        IPv6Assignment::fixChargingByNonExistsOwner();
        TrafficUsage::fixChargingByNonExistsOwner();
        \App\User::fixChargingByNonExistsOwner();
        return 0;
    }
}
