<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-2
 * Time: 下午6:49
 */

namespace Tests\Unit\Charging;


use App\ComputeInstance\TrafficUsage;
use App\Utils\Charging\Common;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ChargingTest extends TestCase
{
    public function testDatabaseSupportCheck()
    {
        Common::databaseSupportCheck();
        $this->assertTrue(true);
    }

    public function testTrafficCharging()
    {
        $networkInterfaceId = 174;
        DB::beginTransaction();
        TrafficUsage::prepareChargeableItems($networkInterfaceId);
        var_dump(TrafficUsage::sumChargingTXByte($networkInterfaceId));
        TrafficUsage::finishCharging($networkInterfaceId);

        TrafficUsage::prepareChargeableItems($networkInterfaceId);
        var_dump(TrafficUsage::sumChargingTXByte($networkInterfaceId));
        DB::rollback();
        $this->assertTrue(true);
    }
}