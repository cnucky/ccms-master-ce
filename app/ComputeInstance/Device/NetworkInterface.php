<?php

namespace App\ComputeInstance\Device;

use App\ComputeInstance;
use App\IPPool\IPv4Assignment;
use App\IPPool\IPv6Assignment;
use Illuminate\Database\Eloquent\Model;

class NetworkInterface extends Model
{
    protected $table = "compute_instance_network_interfaces";

    protected $guarded = [];

    public function instance()
    {
        return $this->belongsTo(ComputeInstance::class, "compute_instance_id");
    }

    public function ipv4Addresses()
    {
        return $this->hasMany(IPv4Assignment::class, "nic_id");
    }

    public function ipv6Addresses()
    {
        return $this->hasMany(IPv6Assignment::class, "nic_id");
    }

    public function trafficUsages()
    {
        return $this->hasMany(ComputeInstance\TrafficUsage::class, "network_interface_id");
    }

    public function bandwidthUsages()
    {
        return $this->hasMany(ComputeInstance\BandwidthUsage::class, "network_interface_id");
    }

    public function releaseIPAddresses($builder = null)
    {
        $this->releaseIPv4Addresses($builder);
        $this->releaseIPv6Addresses($builder);
    }

    public function unbindIPAddresses()
    {
        $this->unbindIPv4Addresses();
        $this->unbindIPv6Addresses();
    }

    public function releaseIPv4Addresses($builder = null)
    {
        $query = $this->ipv4Addresses();
        if (is_callable($builder))
            $builder($query);
        $query->update([
            "user_id" => null,
            "nic_id" => null,
        ]);
    }

    public function unbindIPv4Addresses()
    {
        $this->ipv4Addresses()->where("unbindable", 1)->update(["nic_id" => null]);
    }

    public function releaseIPv6Addresses($builder = null)
    {
        $query = $this->ipv6Addresses();
        if (is_callable($builder))
            $builder($query);
        $query->update([
            "user_id" => null,
            "nic_id" => null,
        ]);
    }

    public function unbindIPv6Addresses()
    {
        $this->ipv6Addresses()->where("unbindable", 1)->update(["nic_id" => null]);
    }
}
