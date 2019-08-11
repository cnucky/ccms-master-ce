<?php

namespace App\IPPool;

use App\ChargeHistory\IPv4PoolChargeHistory;
use App\Node\ComputeNode;
use App\Zone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class IPv4 extends Model
{
    protected $table = "ipv4_pools";

    protected $guarded = [];

    use Assign;

    use IPPool;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assignment()
    {
        return $this->hasMany(IPv4Assignment::class, "pool_id");
    }

    public function assigned()
    {
        return $this->assignment()->whereNotNull("user_id");
    }

    public function zoneAssignments()
    {
        return $this->belongsToMany(Zone::class, "zone_has_ipv4_pools", "pool_id", "zone_id");
    }

    public function nodeAssignments()
    {
        return $this->belongsToMany(ComputeNode::class, "node_has_ipv4_pools", "pool_id", "node_id");
    }

    public function chargeHistories()
    {
        return $this->hasMany(IPv4PoolChargeHistory::class, "pool_id");
    }

    public function prepare($requirements = null)
    {
        (new IPv4AssignmentPrepare($this))->prepare($requirements);
    }

    public function refreshAssignment()
    {
        (new IPv4AssignmentPrepare($this))->refreshHumanReadableIP();
    }
}
