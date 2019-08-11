<?php

namespace App\Http\Controllers\IPPool;

use App\ComputeInstance;
use App\IPPool\IPv6Assignment;
use Illuminate\Http\Request;

class IPv6AssignmentController extends AssignmentController
{
    public function allocatePublicForInstance(Request $request, ComputeInstance $computeInstance)
    {
        return $this->doAllocatePublicForInstance($request, $computeInstance, 6);
    }

    public function allocatePublicForInstanceAsAdmin(Request $request, ComputeInstance $computeInstance)
    {
        return $this->doAllocatePublicForInstanceAsAdmin($request, $computeInstance, 6);
    }

    public function bind(Request $request, IPv6Assignment $assignment, ComputeInstance $computeInstance)
    {
        return $this->bindAssignment($request, $assignment, $computeInstance);
    }

    public function unbind(Request $request)
    {
        return $this->unbindAddress($request, IPv6Assignment::query(), $request->poolId, $request->position);
    }

    public function massUnbind(Request $request)
    {
        return $this->doMassUnbind($request, IPv6Assignment::query());
    }

    public function release(Request $request)
    {
        return $this->releaseAddress($request, IPv6Assignment::query(), $request->poolId, $request->position);
    }

    public function massRelease(Request $request)
    {
        return $this->doMassRelease($request, new IPv6Assignment());
    }

    /**
     * @param Request $request
     * @param IPv6Assignment $assignment
     * @return mixed
     * @throws \App\Exceptions\CCMSAPIException
     */
    public function unbindWithKey(Request $request, IPv6Assignment $assignment)
    {
        return $this->unbindAssignment($request, $assignment);
    }

    public function unbindWithKeyForAdmin(Request $request, IPv6Assignment $assignment)
    {
        return $this->doUnbindAssignment($request, $assignment);
    }

    /**
     * @param Request $request
     * @param IPv6Assignment $assignment
     * @return mixed
     * @throws \App\Exceptions\CCMSAPIException
     */
    public function releaseWithKey(Request $request, IPv6Assignment $assignment)
    {
        return $this->releaseAssignment($request, $assignment);
    }

    public function releaseWithKeyForAdmin(Request $request, IPv6Assignment $assignment)
    {
        return $this->doReleaseAssignment($request, $assignment);
    }

    public function bindableInstances(Request $request, IPv6Assignment $assignment)
    {
        return $this->getBindableInstances($request, $assignment);
    }

    public function convert(IPv6Assignment $assignment)
    {
        return ["result" => true, "unbindable" => $this->doConvert($assignment)];
    }
}
