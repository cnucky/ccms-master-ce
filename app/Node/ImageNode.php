<?php

namespace App\Node;

use Illuminate\Database\Eloquent\Model;

class ImageNode extends Model
{
    protected $fillable = [
        "zone_id",
        "name",
        "description",
        "host",
        "trusted_certificate_id",
        "private_key",
        "certificate",
        "default_zone_image_node",
        "status",
    ];

    protected $hidden = [
        "private_key",
        "certificate",
        "token",
    ];
}
