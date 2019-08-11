<?php

namespace App\Console\Commands\Charging;

use App\User;
use Illuminate\Console\Command;

class LocalVolume extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'charge:lv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Charging for local volumes.';

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
        \App\ComputeInstance\LocalVolume::beginCharging(function ($chargeableItem, User $user, $hourlyUnitPrice, $amount, $chargeTimeInSecond, $price2Charge) {
            $this->info(sprintf("Begin charging for #%s - %s, hourly price: %s, amount: %s, charge seconds: %s, price to charge: %s.", $chargeableItem->id, $chargeableItem->unique_id, $hourlyUnitPrice, $amount, $chargeTimeInSecond, $price2Charge));
            $this->info(sprintf("User #%s - %s's credit before charge: %s.", $user->id, $user->email, $user->credit));
        }, function ($chargeable, User $user, $hourlyUnitPrice, $amount, $chargeTimeInSecond, $price2Charge) {
            $this->info(sprintf("User #%s - %s's credit after charged: %s.\n", $user->id, $user->email, $user->credit));
        });
        return 0;
    }
}
