<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-23
 * Time: 上午12:34
 */

namespace App\Exceptions;


use App\Constants\GlobalErrorCode;
use Illuminate\Contracts\Support\Renderable;

class QuotaExceededException extends \Exception implements Renderable
{
    /**
     * @inheritDoc
     */
    public function render()
    {
        return ["result" => false, "errno" => GlobalErrorCode::QUOTA_EXCEEDED];
    }
}