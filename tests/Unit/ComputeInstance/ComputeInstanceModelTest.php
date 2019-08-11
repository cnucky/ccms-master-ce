<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-20
 * Time: 下午2:34
 */

namespace Tests\Unit\ComputeInstance;


use App\ComputeInstance;
use Tests\TestCase;

class ComputeInstanceModelTest extends TestCase
{
    public function testCalcRecentUsage()
    {
        ComputeInstance::calcInstanceRecent20MinutesUsage(13);
        $this->assertTrue(true);
    }
}