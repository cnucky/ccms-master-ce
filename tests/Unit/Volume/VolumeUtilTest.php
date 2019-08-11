<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-18
 * Time: 下午9:17
 */

namespace Tests\Unit\Volume;


use App\ComputeInstance\LocalVolume;
use App\Utils\Volume\LocalVolumeUtils;
use Tests\TestCase;

class VolumeUtilTest extends TestCase
{
    public function testResize()
    {
        $this->assertTrue($this->getUtils()->resize(22));
    }

    /**
     * @return LocalVolumeUtils
     */
    public function getUtils()
    {
        return LocalVolumeUtils::fromLocalVolumeModel(LocalVolume::query()->orderByDesc("id")->firstOrFail());
    }
}