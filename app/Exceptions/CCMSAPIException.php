<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-21
 * Time: 上午1:05
 */

namespace App\Exceptions;


use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;

class CCMSAPIException extends \Exception implements Renderable
{
    public function render()
    {
        return new JsonResponse(["result" => false, "errno" => $this->getCode(), "message" => $this->getMessage()]);
    }
}