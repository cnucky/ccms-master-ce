<?php

namespace App;

use App\ChargeHistory\ComputeInstancePackageChargeHistory;
use App\Constants\CreditRecordType;
use Illuminate\Database\Eloquent\Model;

class ComputeInstancePackage extends Model
{
    protected $guarded = [
        "created_at",
        "updated_at",
    ];

    public function category()
    {
        return $this->belongsTo(ComputeInstancePackageCategory::class, "category_id");
    }

    public function zones()
    {
        return $this->belongsToMany(Zone::class, "zone_has_compute_instance_packages", "package_id", "zone_id")->withPivot("id", "stock");
    }

    public function instances()
    {
        return $this->hasMany(ComputeInstance::class, "compute_instance_package_id");
    }

    public function stocks()
    {
        return $this->hasMany(ZoneHasComputeInstancePackage::class, "package_id");
    }

    public function chargeHistories()
    {
        return $this->hasMany(ComputeInstancePackageChargeHistory::class, "compute_instance_package_id");
    }

    public function packageSize()
    {
        return [
            "vCPU" => $this->vCPU,
            "memory" => $this->memory,
            "min_storage_capacity" => $this->min_storage_capacity,
            "max_storage_capacity" => $this->max_storage_capacity,
            "public_ipv4" => $this->public_ipv4,
            "public_ipv4_block_size" => $this->public_ipv4_block_size,
            "max_elastic_ipv4_block" => $this->max_elastic_ipv4_block,
            "public_ipv6" => $this->public_ipv6,
            "public_ipv6_block_size" => $this->public_ipv6_block_size,
            "max_elastic_ipv6_block" => $this->max_elastic_ipv6_block,
            "traffic" => $this->traffic,
            "inbound_traffic" => $this->inbound_traffic,
            "outbound_traffic" => $this->outbound_traffic,
            "inbound_bandwidth" => $this->inbound_bandwidth,
            "outbound_bandwidth" => $this->outbound_bandwidth,
            "io_weight" => $this->io_weight,
            "read_bytes_sec" => $this->read_bytes_sec,
            "write_bytes_sec" => $this->write_bytes_sec,
            "read_iops_sec" => $this->read_iops_sec,
            "write_iops_sec" => $this->write_iops_sec,
            "price_per_hour" => $this->price_per_hour,
        ];
    }
}
