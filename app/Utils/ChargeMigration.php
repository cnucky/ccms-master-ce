<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-2
 * Time: 下午2:56
 */

namespace App\Utils;


use Illuminate\Database\Schema\Blueprint;

class ChargeMigration
{
    public static function createChargeColumn(Blueprint $table, $tableName = null)
    {
        $table->unsignedInteger('charging_by')->nullable();
        $table->timestamp('last_charged_at')->nullable();
        $table->timestamp('charging_started_at')->nullable();

        $indexName = null;
        if (!is_null($tableName))
            $indexName = $tableName . "_charge_columns_index";

        $table->index(["charging_by", "last_charged_at", "charging_started_at"], $indexName);
    }
}