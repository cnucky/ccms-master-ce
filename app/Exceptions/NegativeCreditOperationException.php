<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-25
 * Time: 下午1:36
 */

namespace App\Exceptions;


use App\Constants\GlobalErrorCode;
use Illuminate\Contracts\Support\Renderable;

class NegativeCreditOperationException extends \Exception implements Renderable
{
    /**
     * @inheritDoc
     */
    public function render()
    {
        return ["result" => false, "errno" => GlobalErrorCode::NEGATIVE_CREDIT_OPERATION];
    }
}