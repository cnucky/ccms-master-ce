<?php

namespace App;

use App\Node\ComputeNode;
use Illuminate\Database\Eloquent\Model;

class TrustedCertificate extends Model
{
    protected $fillable = [
        "fingerprint",
        "name",
        "certificate",
    ];

    public function computeNode()
    {
        return $this->hasMany(ComputeNode::class);
    }
}
