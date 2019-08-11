<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-13
 * Time: 上午1:05
 */

namespace App\IPPool;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use YunInternet\PHPIPCalculator\CalculatorFactory;
use YunInternet\PHPIPCalculator\Contract\IPCalculator;

abstract class AssignmentPrepare
{
    const MIN_PREPARE = 256;

    /**
     * @var IPv4|IPv6
     */
    private $pool;

    public function __construct(Model $pool)
    {
        $this->pool = $pool;
    }

    /**
     * @param null|int $requirements The amount need to assign now, if is null or smaller than self::MIN_PREPARE, self::MIN_PREPARE used.
     */
    public function prepare($requirements = null)
    {
        if (is_null($requirements) || !is_integer($requirements))
            $requirements = self::MIN_PREPARE;
        else if ($requirements < self::MIN_PREPARE)
            $requirements = self::MIN_PREPARE;

        // Retrieve current free assignment record count, if count >= $max, return
        if ($this->isCurrentFreeAssignmentMoreThanRequirements($requirements))
            return;
        if ($this->getPool()->assignment()->count() >= $this->getPool()->total_subnet)
            return;

        DB::transaction(function () use ($requirements) {
            // Lock the IPPool to prevent parallel prepare
            $this->getPool()->newQuery()->where("id", $this->getPool()->id)->lockForUpdate()->first();

            // Check again after lock relative records
            if ($this->isCurrentFreeAssignmentMoreThanRequirements($requirements, $currentFreeAssignment))
                return;
            if ($this->getPool()->assignment()->count() >= $this->getPool()->total_subnet)
                return;

            $maxPosition = -1;
            // Retrieve the current max position
            $maxAssignmentRecord = $this ->pool->assignment()->orderByDesc("position")->limit(1)->first();
            if ($maxAssignmentRecord)
                $maxPosition = $maxAssignmentRecord->position;

            // Create calculator
            $calculator = (new CalculatorFactory($this->getFirstUsableIP(), $this->pool->subnet_network_bits))->create();
            $assignmentNeed2Create = $requirements - $currentFreeAssignment;


            $lastUsableIP = $this->getLastUsableIP();
            $assignmentValues = [];
            for ($i = 0; $i < $assignmentNeed2Create; ++$i) {
                $position2Create = ++$maxPosition;

                $calculatorAtCurrentPosition = $calculator->getSubnetAfter($position2Create);
                // If first IP located after this pool's last usable ip, stop
                if ($calculator::compare($calculatorAtCurrentPosition->getFirstAddress(), $lastUsableIP) === 1)
                    break;

                $assignmentValues[] = [
                    "pool_id" => $this->pool->id,
                    "position" => $position2Create,
                    "human_readable_first_usable" => $calculatorAtCurrentPosition->getFirstHumanReadableAddress(),
                    "human_readable_last_usable" => $calculatorAtCurrentPosition->getLastHumanReadableAddress(),
                ];
            }

            $this->getAssignmentQueryBuilder()->insert($assignmentValues);
        });
    }

    public function refreshHumanReadableIP()
    {
        DB::transaction(function () {
            $calculator = (new CalculatorFactory($this->getFirstUsableIP(), $this->pool->subnet_network_bits))->create();
            foreach ($this->getPool()->assignment()->lockForUpdate()->get(["pool_id", "position"]) as $assignment) {
                $calculatorAtCurrentPosition = $calculator->getSubnetAfter($assignment->position);
                $assignment->update([
                    "human_readable_first_usable" => $calculatorAtCurrentPosition->getFirstHumanReadableAddress(),
                    "human_readable_last_usable" => $calculatorAtCurrentPosition->getLastHumanReadableAddress(),
                ]);
            }
        });
    }

    /**
     * @return IPv4|IPv6
     */
    public function getPool()
    {
        return $this->pool;
    }

    protected function isCurrentFreeAssignmentMoreThanRequirements($requirements, &$currentFreeAssignment = null)
    {
        // Retrieve current free assignment record count, if count >= $max, return
        $currentFreeAssignment = $this->pool->assignment()->whereNull("user_id")->count();
        return $currentFreeAssignment >= $requirements;
    }

    /**
     * @return IPCalculator
     */
    abstract protected function getFirstUsableIP();

    /**
     * @return int|int[]
     */
    abstract protected function getLastUsableIP();

    /**
     * @return Builder
     */
    abstract protected function getAssignmentQueryBuilder();
}