<?php

namespace App\Jobs\ComputeInstance;

use App\ComputeInstance;
use App\Constants\ComputeInstanceStatusCode;
use App\Constants\IPPool\TypeCode;
use App\IPPool\IPv4;
use App\Node\ComputeNode;
use App\Utils\ComputeInstance\ConfigurationBuilder;
use App\Utils\Node\ComputeNode\ComputeNodeUtil;
use App\Utils\Node\ComputeNode\Exception\Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use YunInternet\CCMSCommon\Constants\Constants;
use YunInternet\CCMSCommon\Constants\NetworkType;
use YunInternet\CCMSCommon\Utils\DomainXML;

class Setup extends OperationRequest
{
    /**
     * Execute the job.
     *
     * @return void|int
     * @throws \Throwable
     */
    protected function operationRequestHandle()
    {
        /**
         * @var ComputeInstance $computeInstance
         */
        $computeInstance = $this->getComputeInstanceOperationRequest()->instance;

        /**
         * @var ComputeNodeUtil $util
         */
        $util = $computeInstance->node->createUtil();

        // Send setup request to slave server
        $response = $util->instanceSetupRequest(ConfigurationBuilder::buildConfiguration($computeInstance));

        $computeInstance->status = ComputeInstanceStatusCode::STATUS_NORMAL;
        $computeInstance->power_status = ComputeInstanceStatusCode::POWER_STATUS_RUNNING;


        // Get uuid from domain XML
        try {
            // $domainXMLElement = new \SimpleXMLElement($response->domainXML);
            // $uuid = $response->uuid;
            $computeInstance->uuid = $response->uuid;

            // Store mac addresses
            $macAddresses = $response->macAddresses;
            if (@$macAddresses[NetworkType::TYPE_PUBLIC_NETWORK]) {
                $computeInstance->networkInterfaces()->where("type", NetworkType::TYPE_PUBLIC_NETWORK)->update([
                    "mac_address" => $macAddresses[NetworkType::TYPE_PUBLIC_NETWORK],
                ]);
            }
            if (@$macAddresses[NetworkType::TYPE_PRIVATE_NETWORK]) {
                $computeInstance->networkInterfaces()->where("type", NetworkType::TYPE_PRIVATE_NETWORK)->update([
                    "mac_address" => $macAddresses[NetworkType::TYPE_PRIVATE_NETWORK],
                ]);
            }
        } catch (\Throwable $throwable) {}

        $computeInstance->attachedLocalVolumes()->update([
            "status" => \App\Utils\Volume\Constants::STATUS_NORMAL,
        ]);
        $computeInstance->save();
    }

    protected function onFailed()
    {
        try {
            $this->getComputeInstance()->update(["status" => ComputeInstanceStatusCode::STATUS_CREATE_UNSUCCESSFULLY]);
            $this->getComputeInstance()->attachedLocalVolumes()->update(["status" => \App\Utils\Volume\Constants::STATUS_CREATE_UNSUCCESSFULLY]);
            /**
             * @var ComputeInstance\Device\NetworkInterface $networkInterface
             */
            // $networkInterface->releaseIPAddresses();
        } catch (\Throwable $e) {
        }
    }
}
