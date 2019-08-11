<?php

namespace App\IPPool;

use App\Constants\CreditRecordType;
use Illuminate\Database\Eloquent\Model;

class IPv4Assignment extends Model
{
    const VERSION = 4;

    use NetworkInterface;

    use IPAssignment;

    protected $table = "ipv4_assignments";

    protected $primaryKey = "id";

    protected $guarded = [];

    public function pool()
    {
        return $this->belongsTo(IPv4::class, "pool_id");
    }

    public function release()
    {
        $this->update([
            "user_id" => null,
            "nic_id" => null,
        ]);
    }

    public function getCreditRecordType(): int
    {
        return CreditRecordType::TYPE_CHARGE_ELASTIC_IP_V4;
    }
}
