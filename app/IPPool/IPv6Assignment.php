<?php

namespace App\IPPool;

use App\Constants\CreditRecordType;
use Illuminate\Database\Eloquent\Model;

class IPv6Assignment extends Model
{
    const VERSION = 6;

    use NetworkInterface;

    use IPAssignment;

    protected $table = "ipv6_assignments";

    protected $primaryKey = "id";

    protected $guarded = [];

    public function pool()
    {
        return $this->belongsTo(IPv6::class, "pool_id");
    }

    public function getCreditRecordType(): int
    {
        return CreditRecordType::TYPE_CHARGE_ELASTIC_IP_V6;
    }
}
