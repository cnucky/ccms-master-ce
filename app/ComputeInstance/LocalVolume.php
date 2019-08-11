<?php

namespace App\ComputeInstance;

use App\ChargeHistory\ComputeNodeChargeHistory;
use App\ComputeInstance;
use App\ComputeResourceOperationRequest;
use App\Constants\ComputeResourceOperation\ResourceTypeCode;
use App\Constants\ComputeResourceOperation\StatusCode;
use App\Constants\CreditRecordType;
use App\Image;
use App\Node\ComputeNode;
use App\User;
use App\Utils\Charging\ChargeableModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LocalVolume extends Model
{
    use ChargeableModel;

    protected $table = "compute_instance_local_volumes";

    protected $guarded = [];

    public function instance()
    {
        return $this->belongsTo(ComputeInstance::class, "attached_compute_instance_id");
    }

    public function image()
    {
        return $this->belongsTo(Image::class);
    }

    public function node()
    {
        return $this->belongsTo(ComputeNode::class, "compute_node_id");
    }

    public function operationRequests()
    {
        return $this->hasMany(ComputeResourceOperationRequest::class, "resource_id")->where("resource_type", ResourceTypeCode::TYPE_LOCAL_VOLUME);
    }

    public function processingOperationRequests()
    {
        return $this->operationRequests()->where("is_processed", 0);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getHourlyUnitPrice(): string
    {
        return $this->node->zone->storage_price_per_hour_per_gib;
    }

    public function getAmount()
    {
        return $this->capacity;
    }

    public function getCreditRecordDescription($lastChargeTime, $currentChargingTime, $chargeTimeInSecond)
    {
        return sprintf("%s %sGiB: %s - %s (%s seconds)", $this->unique_id, $this->capacity, $lastChargeTime, $currentChargingTime, $chargeTimeInSecond);
    }

    public function getCreditRecordType(): int
    {
        return CreditRecordType::TYPE_CHARGE_LOCAL_VOLUME;
    }

    protected function createRelativeChargeHistory($amount, $time)
    {
        ComputeNodeChargeHistory::query()->create([
            "compute_node_id" => $this->compute_node_id,
            "type" => CreditRecordType::TYPE_CHARGE_LOCAL_VOLUME,
            "amount" => $amount,
            "created_at" => $time,
        ]);
    }

    public function deleteWithNodeCounterUpdate()
    {
        DB::transaction(function () {
            $capacity = $this->capacity;
            $compute_node_id = $this->compute_node_id;
            if ($this->delete()) {
                DB::update("UPDATE compute_nodes SET 
  total_allocated_disk_in_gib_unit = GREATEST(CAST(total_allocated_disk_in_gib_unit AS SIGNED) - ?, 0),
  local_volume_counter = GREATEST(CAST(local_volume_counter AS SIGNED) - 1, 0)
WHERE id = ?", [$capacity, $compute_node_id]);
            }
        });
    }

    public static function calculateTotalHourlyPrice()
    {
        return DB::select(<<<EOF
SELECT IFNULL(SUM(compute_instance_local_volumes.capacity * IFNULL(compute_instance_local_volumes.override_price_per_hour, zones.storage_price_per_hour_per_gib)), 0) AS total_price_per_hour
FROM ((compute_instance_local_volumes INNER JOIN compute_nodes ON compute_instance_local_volumes.compute_node_id = compute_nodes.id)
       INNER JOIN zones ON compute_nodes.zone_id = zones.id)
EOF
        )[0]->total_price_per_hour;
    }
}
