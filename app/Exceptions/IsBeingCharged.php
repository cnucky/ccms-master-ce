<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-22
 * Time: 下午7:04
 */

namespace App\Exceptions;


use App\Constants\GlobalErrorCode;
use Illuminate\Contracts\Support\Renderable;

class IsBeingCharged extends \Exception implements Renderable
{
    /**
     * @inheritDoc
     */
    public function render()
    {
        return ["result" => false, "errno" => GlobalErrorCode::IS_BEING_CHARGED];
    }
}