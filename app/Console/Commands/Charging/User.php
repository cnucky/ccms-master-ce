<?php

namespace App\Console\Commands\Charging;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class User extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'charge:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Charge users\' traffic usages, etc.';

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
        \App\User::clearCharging();
        try {
            while (\App\User::prepareChargeableItems(1, $chargeTime)) {
                /**
                 * @var \App\User $user
                 */
                foreach (\App\User::getChargeableUsers() as $user) {
                    $user->chargeTrafficUsages(function (\App\User $user, $trafficShareGroupId, $totalRXBytes, $totalTXBytes, $userFreeTrafficAtGroup, $totalTXBytes2Charge, $txUnitPrice, $rxUnitPrice, $price2Charge) {
                        $this->info(sprintf("User: #%s - %s, traffic share group id: %s, total rx bytes: %s, total tx bytes: %s, user free traffic at group: %s, total tx bytes to charge: %s, tx unit price: %s, rx unit price: %s, price to charge: %s, user credit: %s.", $user->id, $user->email, $trafficShareGroupId, $totalRXBytes, $totalTXBytes, $userFreeTrafficAtGroup, $totalTXBytes2Charge, $txUnitPrice, $rxUnitPrice, $price2Charge, $user->credit));
                    }, function (\App\User $user, $trafficShareGroupId, $totalRXBytes, $totalTXBytes, $userFreeTrafficAtGroup, $totalTXBytes2Charge, $txUnitPrice, $rxUnitPrice, $price2Charge) {
                        $user->refresh();
                        $this->info(sprintf("User credit: %s.", $user->credit));
                    }, function (\App\User $user, $reason, $trafficShareGroupId, $totalRXBytes, $totalTXBytes, $userFreeTrafficAtGroup, $totalTXBytes2Charge) {
                        $this->info(sprintf("User: #%s - %s, charge skip, reason: %s, total rx bytes: %s, total tx bytes: %s, user free traffic at group: %s, total tx bytes to charge: %s.", $user->id, $user->email, $reason, $totalRXBytes, $totalTXBytes, $userFreeTrafficAtGroup, $totalTXBytes2Charge));
                    });

                    $user->finishCharging($chargeTime);

                    $this->line("");
                }
            }
        } finally {
            \App\User::clearCharging();
        }
        return 0;
    }
}
