<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-13
 * Time: 上午1:21
 */

namespace Tests\Unit;


use App\IPPool\IPv4;
use App\IPPool\IPv4AssignmentPrepare;
use App\IPPool\IPv6;
use App\IPPool\IPv6AssignmentPrepare;
use Tests\TestCase;

class IPPoolAssignmentPrepare extends TestCase
{
    public function testIPv4AssignmentPrepare()
    {
        foreach (IPv4::all() as $item) {
            $assignmentPrepare = new IPv4AssignmentPrepare($item);
            $assignmentPrepare->prepare();
        }

        $this->assertTrue(true);
    }

    public function testIPv6AssignmentPrepare()
    {
        foreach (IPv6::all() as $item) {
            $assignmentPrepare = new IPv6AssignmentPrepare($item);
        $assignmentPrepare->prepare();
        }

        $this->assertTrue(true);
    }
}