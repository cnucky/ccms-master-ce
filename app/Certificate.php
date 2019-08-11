<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Certificate extends Model
{
    protected $guarded = [
        "id",
    ];

    protected $hidden = [
        "privateKey",
        "certificate",
    ];

    public static function encrypt($plainText)
    {
        return Crypt::encryptString($plainText);
    }

    public static function decrypt($encryptedText)
    {
        return Crypt::decryptString($encryptedText);
    }
}
