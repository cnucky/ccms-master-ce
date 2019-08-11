<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-13
 * Time: 上午1:16
 */

namespace App\IPPool;


use Illuminate\Database\Eloquent\Builder;
use YunInternet\PHPIPCalculator\Contract\IPCalculator;

class IPv6AssignmentPrepare extends AssignmentPrepare
{
    protected function getFirstUsableIP()
    {
        return [
            $this->getPool()->first_usable_ip_part_0,
            $this->getPool()->first_usable_ip_part_1,
            $this->getPool()->first_usable_ip_part_2,
            $this->getPool()->first_usable_ip_part_3,
        ];
    }

    protected function getLastUsableIP()
    {
        return [
            $this->getPool()->last_usable_ip_part_0,
            $this->getPool()->last_usable_ip_part_1,
            $this->getPool()->last_usable_ip_part_2,
            $this->getPool()->last_usable_ip_part_3,
        ];
    }

    protected function getAssignmentQueryBuilder()
    {
        return IPv6Assignment::query();
    }
}