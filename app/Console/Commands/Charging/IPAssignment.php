<?php

namespace App\Console\Commands\Charging;

use App\IPPool\IPv4Assignment;
use App\IPPool\IPv6Assignment;
use App\User;
use Illuminate\Console\Command;

class IPAssignment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'charge:ip';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Charging IP assignment.';

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
        $beforeCallback = function ($chargeableItem, User $user, $hourlyUnitPrice, $amount, $chargeTimeInSecond, $price2Charge) {
            $this->info(sprintf("Begin charging for #%s - %s, hourly price: %s, amount: %s, charge seconds: %s, price to charge: %s.", $chargeableItem->id, $chargeableItem->human_readable_first_usable, $hourlyUnitPrice, $amount, $chargeTimeInSecond, $price2Charge));
            $this->info(sprintf("User #%s - %s's credit before charge: %s.", $user->id, $user->email, $user->credit));
        };
        $afterCallback = function ($chargeableItem, User $user, $hourlyUnitPrice, $amount, $chargeTimeInSecond, $price2Charge) {
            $this->info(sprintf("User #%s - %s's credit after charged: %s.\n", $user->id, $user->email, $user->credit));
        };
        $onError = function (\Throwable $throwable) {
            $this->error("Error: " . $throwable->getMessage());
        };

        IPv4Assignment::beginCharging($beforeCallback, $afterCallback, $onError);
        IPv6Assignment::beginCharging($beforeCallback, $afterCallback, $onError);
        return 0;
    }
}
