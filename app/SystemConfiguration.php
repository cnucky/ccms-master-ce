<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SystemConfiguration extends Model
{
    public $incrementing = false;

    protected $primaryKey = "name";

    protected $fillable = [
        "name",
        "value",
    ];

    public static function systemHost()
    {
        return self::value("SYSTEM_HOST");
    }

    public static function value($name)
    {
        if ($foundItem = self::query()->find($name))
            return $foundItem->value;
        return null;
    }

    public static function setValue($name, $value)
    {
        self::query()->updateOrCreate(["name" => $name], ["value" => $value]);
    }

    public static function setValueIfNotExists($name, $value)
    {
        self::query()->firstOrCreate(["name" => $name], ["value" => $value]);
    }
}
