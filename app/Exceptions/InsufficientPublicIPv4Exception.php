<?php
/**
 * Created by PhpStorm.
 * Date: 19-5-9
 * Time: 下午9:11
 */

namespace App\Exceptions;


use App\Constants\GlobalErrorCode;
use Illuminate\Contracts\Support\Renderable;

class InsufficientPublicIPv4Exception extends InsufficientResourceException implements Renderable
{
    /**
     * @inheritDoc
     */
    public function render()
    {
        return ["result" => false, "errno" => GlobalErrorCode::INSUFFICIENT_PUBLIC_IP_V4];
    }
}