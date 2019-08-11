<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-13
 * Time: 上午1:14
 */

namespace App\IPPool;


use Illuminate\Database\Eloquent\Builder;
use YunInternet\PHPIPCalculator\Contract\IPCalculator;

class IPv4AssignmentPrepare extends AssignmentPrepare
{
    protected function getFirstUsableIP()
    {
        return $this->getPool()->first_usable_ip;
    }

    protected function getLastUsableIP()
    {
        return $this->getPool()->last_usable_ip;
    }

    protected function getAssignmentQueryBuilder()
    {
        return IPv4Assignment::query();
    }
}