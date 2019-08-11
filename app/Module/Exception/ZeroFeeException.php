<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-8
 * Time: 下午8:37
 */

namespace App\Module\Exception;


use App\Constants\GlobalErrorCode;
use Illuminate\Contracts\Support\Renderable;

class ZeroFeeException extends PaymentModuleException implements Renderable
{
    /**
     * @inheritDoc
     */
    public function render()
    {
        return ["result" => false, "errno" => GlobalErrorCode::ZERO_FEE];
    }
}