<?php

namespace App\Jobs\ComputeInstance;

use App\ComputeInstance\Device\NetworkInterface;

class ChangeNetworkInterfaceModel extends OperationRequest
{
    protected function operationRequestHandle()
    {
        $data = $this->getJSONDecodedData();
        $networkInterface = NetworkInterface::query()->findOrFail($data->networkInterfaceId);
        $this->getComputeInstance()->createUtils()->changeNetworkInterfaceModel($networkInterface->mac_address, $data->model);
        $networkInterface->update(["model" => $data->model]);
    }
}
