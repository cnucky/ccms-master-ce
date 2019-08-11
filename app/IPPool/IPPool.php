<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-26
 * Time: 上午10:30
 */

namespace App\IPPool;


use App\Node\ComputeNode;

trait IPPool
{
    public function getBindableNodeList()
    {
        $directZoneList = $this->zoneAssignments()->pluck("id")->toArray();
        $directNodeList = $this->nodeAssignments()->pluck("id")->toArray();
        $nodeListViaZone = ComputeNode::query()->whereIn("zone_id", $directZoneList)->pluck("id")->toArray();

        return array_merge($directNodeList, $nodeListViaZone);
    }
}