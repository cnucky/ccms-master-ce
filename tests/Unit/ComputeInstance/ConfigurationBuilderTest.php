<?php
/**
 * Created by PhpStorm.
 * Date: 19-2-19
 * Time: 下午3:51
 */

namespace Tests\Unit\ComputeInstance;


use App\ComputeInstance;
use App\Utils\ComputeInstance\ConfigurationBuilder;
use Tests\TestCase;

class ConfigurationBuilderTest extends TestCase
{
    public function testBuild()
    {
        $configurationBuilder = new ConfigurationBuilder(ComputeInstance::query()->firstOrFail());
        print_r($configurationBuilder->build());
        $this->assertTrue(true);
    }
}