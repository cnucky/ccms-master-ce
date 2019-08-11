<?php
/**
 * Created by PhpStorm.
 * Date: 19-5-7
 * Time: 下午11:26
 */

namespace App\Exceptions;


use App\Constants\GlobalErrorCode;
use Illuminate\Contracts\Support\Renderable;

class OperationRequestLimitException extends \Exception implements Renderable
{
    /**
     * @inheritDoc
     */
    public function render()
    {
        return ["result" => false, "errno" => GlobalErrorCode::OPERATION_REQUEST_LIMIT];
    }
}