<?php

namespace App\Http\Controllers\IPPool;

use App\ComputeInstance;
use App\IPPool\IPv4Assignment;
use Illuminate\Http\Request;

class IPv4AssignmentController extends AssignmentController
{
    public function allocatePublicForInstance(Request $request, ComputeInstance $computeInstance)
    {
        return $this->doAllocatePublicForInstance($request, $computeInstance, 4);
    }

    public function allocatePublicForInstanceAsAdmin(Request $request, ComputeInstance $computeInstance)
    {
        return $this->doAllocatePublicForInstanceAsAdmin($request, $computeInstance, 4);
    }

    public function unbind(Request $request)
    {
        return $this->unbindAddress($request, IPv4Assignment::query(), $request->poolId, $request->position);
    }

    public function massUnbind(Request $request)
    {
        return $this->doMassUnbind($request, IPv4Assignment::query());
    }

    public function release(Request $request)
    {
        return $this->releaseAddress($request, IPv4Assignment::query(), $request->poolId, $request->position);
    }

    public function massRelease(Request $request)
    {
        return $this->doMassRelease($request, new IPv4Assignment());
    }

    public function bind(Request $request, IPv4Assignment $assignment, ComputeInstance $computeInstance)
    {
        return $this->bindAssignment($request, $assignment, $computeInstance);
    }

    /**
     * @param Request $request
     * @param IPv4Assignment $assignment
     * @return mixed
     * @throws \App\Exceptions\CCMSAPIException
     */
    public function unbindWithKey(Request $request, IPv4Assignment $assignment)
    {
        return $this->unbindAssignment($request, $assignment);
    }

    public function unbindWithKeyForAdmin(Request $request, IPv4Assignment $assignment)
    {
        return $this->doUnbindAssignment($request, $assignment);
    }

    /**
     * @param Request $request
     * @param IPv4Assignment $assignment
     * @return mixed
     * @throws \App\Exceptions\CCMSAPIException
     */
    public function releaseWithKey(Request $request, IPv4Assignment $assignment)
    {
        return $this->releaseAssignment($request, $assignment);
    }

    public function releaseWithKeyForAdmin(Request $request, IPv4Assignment $assignment)
    {
        return $this->doReleaseAssignment($request, $assignment);
    }

    public function bindableInstances(Request $request, IPv4Assignment $assignment)
    {
        return $this->getBindableInstances($request, $assignment);
    }

    public function convert(IPv4Assignment $assignment)
    {
        return ["result" => true, "unbindable" => $this->doConvert($assignment)];
    }
}
