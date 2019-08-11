<?php

namespace App\Console\Commands\User\Inactive;

use App\ComputeInstance;
use App\ComputeInstance\OperationRequest;
use App\ComputeResourceOperationRequest;
use App\Constants\AvailableSystemConfigurations;
use App\Constants\ComputeInstance\OperationRequest\TypeCode;
use App\Constants\ComputeResourceOperation\ResourceTypeCode;
use App\Constants\UserInactiveStatusCodes;
use App\SystemConfiguration;
use App\User;
use App\Utils\Common;
use Illuminate\Console\Command;

class Set extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:inactive:set';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Release user\'s resources and set to inactive.';

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
        $autoReleaseAfter = SystemConfiguration::value(AvailableSystemConfigurations::AUTO_RELEASE_LC_USER_RESOURCE_AFTER);
        if (!is_numeric($autoReleaseAfter))
            return 0;
        $autoReleaseAfter = intval($autoReleaseAfter);
        $maxTime = date("Y-m-d H:i:s", time() - $autoReleaseAfter * 3600);

        $updatedRecordCount = $this->makeBasicUserQueryBuilder($maxTime)
            ->whereDoesntHave("instances")
            ->whereDoesntHave("localVolumes")
            ->whereDoesntHave("ipv4s")
            ->whereDoesntHave("ipv6s")
            ->update([
                "inactive" => UserInactiveStatusCodes::STATUS_INACTIVE,
            ]);

        /**
         * @var User $user
         */
        foreach ($this->makeBasicUserQueryBuilder($maxTime)->get() as $user) {
            $this->info("Release user #" . $user->id . " " . $user->email . "'s resources.");

            foreach ($user->instances as $instance) {
                try {
                    $this->releaseInstance($instance);
                } catch (\Throwable $throwable) {
                    Common::logException($throwable);
                }
            }

            foreach ($user->localVolumes as $localVolume) {
                try {
                    $this->releaseVolume($localVolume);
                } catch (\Throwable $throwable) {
                    Common::logException($throwable);
                }
            }

            $user->ipv4s()->update([
                "user_id" => null,
                "nic_id" => null,
            ]);
            $user->ipv6s()->update([
                "user_id" => null,
                "nic_id" => null,
            ]);
        }

        $this->info("$updatedRecordCount user(s) updated.");

        return 0;
    }

    private function makeBasicUserQueryBuilder($maxTime)
    {
        return User::query()
            ->where("inactive", UserInactiveStatusCodes::STATUS_PENDING)
            ->where("credit", "<", "0")
            ->where("start_lack_of_credit_at", "<=", $maxTime);
    }

    private function releaseInstance(ComputeInstance $computeInstance)
    {
         OperationRequest::newRequestThenDispatch($computeInstance, TypeCode::TYPE_DESTROY, [
            "deleteAttachedVolumes" => false,
            "releaseExtraIPs" => false,
        ]);
    }

    private function releaseVolume(ComputeInstance\LocalVolume $localVolume)
    {
        ComputeResourceOperationRequest::newOperationRequestThenDispatch($localVolume->user_id, ResourceTypeCode::TYPE_LOCAL_VOLUME, $localVolume->id, \App\Constants\Volume\OperationRequest\TypeCode::TYPE_RELEASE);
    }
}
