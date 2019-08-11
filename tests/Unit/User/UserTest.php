<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-22
 * Time: 下午8:45
 */

namespace Tests\Unit\User;


use App\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testAutoChangeUserQuota()
    {
        User::query()->firstOrFail()->autoChangeUserQuota();
        $this->assertTrue(true);
    }

    public function testPrepareForExclusiveOperation()
    {
        $this->assertTrue(User::query()->firstOrFail()->prepareForExclusiveOperation() > 0);
    }

    public function testCalculateFreeTXTrafficAtShareGroup()
    {
        User::query()->firstOrFail()->calculateFreeTXTrafficAtShareGroup(1);
        $this->assertTrue(true);
    }
}