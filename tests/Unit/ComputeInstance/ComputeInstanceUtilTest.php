<?php
/**
 * Created by PhpStorm.
 * Date: 19-2-28
 * Time: 下午11:02
 */

namespace Tests\Unit\ComputeInstance;


use App\ComputeInstance;
use App\Utils\ComputeInstance\ComputeInstanceUtils;
use App\Utils\ComputeInstance\ConfigurationBuilder;
use Tests\TestCase;
use YunInternet\CCMSCommon\Constants\Domain\Device\Disk\DiskDeviceCode;

class ComputeInstanceUtilTest extends TestCase
{
    public function testPowerOn()
    {
        $this->getUtil()->powerOn();
        $this->assertTrue(true);
    }

    public function testPowerReset()
    {
        $this->getUtil()->powerReset();
        $this->assertTrue(true);
    }

    public function testPowerOff()
    {
        $this->getUtil()->powerOff();
        $this->assertTrue(true);
    }

    public function testChangeMedia()
    {
        $this->assertTrue($this->getUtil()->changeMedia(DiskDeviceCode::DEVICE_CDROM, 0, "debian-9.8.0-amd64-netinst"));
    }

    public function testEjectMedia()
    {
        $this->assertTrue($this->getUtil()->changeMedia(DiskDeviceCode::DEVICE_CDROM, 0, null));
    }

    public function testUpdateIPAddress()
    {
        $this->assertTrue($this->getUtil()->updateIPAddress((new ConfigurationBuilder($this->getInstance()))->networkInterfaces($this->getInstance()->networkInterfaces()->firstOrFail()->mac_address)[0]));
    }

    /**
     * @return ComputeInstanceUtils
     */
    public function getUtil()
    {
        return ComputeInstanceUtils::fromComputeInstanceModel($this->getInstance());
    }

    /**
     * @return ComputeInstance
     */
    public function getInstance()
    {
        return ComputeInstance::query()->firstOrFail();
    }
}