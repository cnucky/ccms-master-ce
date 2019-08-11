<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-30
 * Time: 上午11:41
 */

namespace Tests\Unit\Model;


use App\ComputeInstance;
use App\ComputeInstance\BandwidthUsage;
use App\ZoneHasComputeInstancePackage;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class Test extends TestCase
{
    public function testForeignKeyName()
    {
        try {
            BandwidthUsage::query()->create([
                "network_interface_id" => 0xFFFFFFFF,
                "id" => 0xFFFFFFFF,
                "rx_bytes_per_second" => 0,
                "rx_packages_per_second" => 0,
                "tx_bytes_per_second" => 0,
                "tx_packages_per_second" => 0,
                "microtime" => microtime(true),
            ]);
        } catch (QueryException $e) {
            $this->assertEquals(23000, $e->getCode());
        }
    }

    public function testChargeableModelPrepare()
    {
        $this->assertEquals(1, ComputeInstance::prepareChargeableItems(1));
        foreach (ComputeInstance::getChargeableItems() as $chargeableItem) {
            var_dump($chargeableItem->id);
        }
        return;
    }

    public function testChargeableModelPrepareFixOwner()
    {
        var_dump(ComputeInstance::fixChargingByNonExistsOwner());
        $this->assertTrue(true);
    }

    public function testOutOfRange()
    {
        ZoneHasComputeInstancePackage::query()->update(["stock" => -1]);
        $this->assertTrue(true);
    }
}