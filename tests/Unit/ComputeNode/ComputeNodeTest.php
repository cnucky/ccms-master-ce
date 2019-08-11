<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-12
 * Time: 下午9:18
 */

namespace Tests\Unit\ComputeNode;


use App\Node\ComputeNode;
use App\Zone;
use Tests\TestCase;

class ComputeNodeTest extends TestCase
{
    public function testCalculateAllocatedMemory()
    {
        $this->getComputeNode()->calculateAllocatedMemory();
        $this->assertTrue(true);
    }

    public function testCalculateAllocatedDiskCapacity()
    {
        $this->getComputeNode()->calculateAllocatedDiskCapacity();
        $this->assertTrue(true);
    }

    public function testAssignNode()
    {
        Zone::query()->firstOrFail()->assignNode(1024, 32, function ($nodeId) {
            var_dump($nodeId);
            throw new \Exception();
        });
    }

    public function testAllocateMemory()
    {
        $this->getComputeNode()->allocateMemory(2048, function ($nodeId) {
            var_dump($nodeId);
            throw new \Exception();
        });
    }

    public function testAllocateDiskCapacity()
    {
        $this->getComputeNode()->allocateDiskCapacity(32, function ($nodeId) {
            var_dump($nodeId);
            throw new \Exception();
        });
    }

    public function testRefreshComputeInstanceCounter()
    {
        $this->getComputeNode()->refreshComputeInstanceCounter();
        $this->assertTrue(true);
    }

    public function testRefreshLocalVolumeCounter()
    {
        $this->getComputeNode()->refreshLocalVolumeCounter();
        $this->assertTrue(true);
    }

    private function getComputeNode() : ComputeNode
    {
        return ComputeNode::query()->firstOrFail();
    }
}