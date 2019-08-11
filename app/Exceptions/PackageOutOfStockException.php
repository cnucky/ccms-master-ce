<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-23
 * Time: 上午2:34
 */

namespace App\Exceptions;


use App\Constants\GlobalErrorCode;
use Illuminate\Contracts\Support\Renderable;

class PackageOutOfStockException extends \Exception implements Renderable
{
    /**
     * @inheritDoc
     */
    public function render()
    {
        return ["result" => false, "errno" => GlobalErrorCode::PACKAGE_OUT_OF_STOCK];
    }
}