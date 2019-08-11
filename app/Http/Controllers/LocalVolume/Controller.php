<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-26
 * Time: 下午4:48
 */

namespace App\Http\Controllers\LocalVolume;


use Illuminate\Http\Request;

trait Controller
{
    public static function whereBelong2UserClosure(Request $request)
    {
        return function ($builder) use ($request) {
            $builder->where("user_id", $request->user()->id);
        };
    }

    public static function whereNotProtected($builder)
    {
        $builder->where("protected", "=", 0);
    }

    public static function whereNotProtectedClosure()
    {
        return function ($builder) {
            $builder->where("protected", "=", 0);
        };
    }
}