<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-15
 * Time: 下午7:15
 */

namespace App\IPPool;


use Illuminate\Support\Facades\DB;

trait Assign
{
    public function assignWithAutoPrepare($userId, $nicId, $amount, $unbindable = 0)
    {
        if ($this->assignWithoutAutoPrepare($userId, $nicId, $amount, $unbindable) === false) {
            $this->prepare($amount);
            return $this->assignWithoutAutoPrepare($userId, $nicId, $amount, $unbindable);
        }
        return true;
    }

    public function assignWithoutAutoPrepare($userId, $nicId, $amount, $unbindable = 0)
    {
        $amount = intval($amount);
        DB::beginTransaction();
        $assignedCount = $this->assignment()
            ->whereNull("user_id")
            ->limit($amount)->update([
                "user_id" => $userId,
                "nic_id" => $nicId,
                "unbindable" => $unbindable,
                "assigned_at" => date("Y-m-d H:i:s"),
                "last_charged_at" => null,
            ]);
        if ($assignedCount !== $amount) {
            DB::rollback();
            return false;
        }
        DB::commit();
        return true;
    }
}