<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-26
 * Time: 下午1:47
 */

namespace App\IPPool;


use App\ComputeInstance;
use App\Node\ComputeNode;
use App\Utils\Node\ComputeNode\Exception\Exception;
use Illuminate\Database\Eloquent\Builder;

class Common
{
    /**
     * @param int $ipVersion 4 or 6
     * @param ComputeInstance\Device\NetworkInterface $networkInterface
     * @param bool $asExtraIP
     * @param int|null $blockSize If null, allocate smallest
     * @param int $nBlock
     * @param bool $fromNodeFirst
     * @throws Exception
     */
    public static function assignIPForNetworkInterface($ipVersion, ComputeInstance\Device\NetworkInterface $networkInterface, $asExtraIP = false, $blockSize = null, $nBlock = 1, $fromNodeFirst = false)
    {
        $type = $networkInterface->type;
        /**
         * @var ComputeInstance $computeInstance
         */
        $computeInstance = $networkInterface->instance;

        /**
         * @var ComputeNode $computeNode
         */
        $computeNode = $computeInstance->node;

        $whereCondition = [
            "type" => $type,
        ];

        $unbindable = 0;

        if ($asExtraIP) {
            $unbindable = 1;
            $whereCondition["assign_for_extra_ip"] = 1;
        } else {
            $whereCondition["assign_for_new_instance"] = 1;
        }

        if (!is_null($blockSize)) {
            $whereCondition["subnet_network_bits"] = $blockSize;
        }

        $relationName = "ipv6Pools";
        if ($ipVersion == 4) {
            $relationName = "ipv4Pools";
        }

        if ($fromNodeFirst) {
            if ($ipVersion == 4) {
                $firstBuilder = $computeNode->{$relationName}();
                $secondBuilder = $computeNode->zone->{$relationName}();
            }
        } else {
            $firstBuilder = $computeNode->zone->{$relationName}();
            $secondBuilder = $computeNode->{$relationName}();
        }

        $firstBuilder->where($whereCondition);
        $secondBuilder->where($whereCondition);
        self::commonBuilderOperation($firstBuilder);
        self::commonBuilderOperation($secondBuilder);


        if (self::assignIPFromIPPool($firstBuilder, $nBlock, $computeInstance->user_id, $networkInterface->id, $unbindable) === false) {
            // If tried to assigned from first unsuccessfully, try second
            if (self::assignIPFromIPPool($secondBuilder, $nBlock, $computeInstance->user_id, $networkInterface->id, $unbindable) === false)
                throw new Exception("IP insufficient");
        }
    }

    /**
     * @param Builder $ipPoolBuilder
     * @param $amount
     * @param int $userId
     * @param int $nicId
     * @param int $unbindable
     * @return bool
     */
    public static function assignIPFromIPPool($ipPoolBuilder, $amount, $userId, $nicId, $unbindable = 0)
    {
        $assignResult = false;

        /**
         * @var IPv6|IPv6 $ipPool
         */
        foreach ($ipPoolBuilder->get() as $ipPool) {
            $assignResult = $ipPool->assignWithAutoPrepare($userId, $nicId, $amount, $unbindable);
            if ($assignResult)
                break;
        }

        return $assignResult;
    }

    protected static function commonBuilderOperation($builder)
    {
        $builder->orderByDesc("subnet_network_bits");
    }
}