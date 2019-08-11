<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-13
 * Time: 上午12:23
 */

namespace App\Exceptions;


use App\Constants\GlobalErrorCode;
use Illuminate\Contracts\Support\Renderable;

class InsufficientResourceException extends \Exception implements Renderable
{
    /**
     * @inheritDoc
     */
    public function render()
    {
        return ["result" => false, "errno" => GlobalErrorCode::INSUFFICIENT_RESOURCE];
    }
}