<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-5
 * Time: 下午8:30
 */

namespace App\Module\Exception;


use App\Constants\GlobalErrorCode;
use Illuminate\Contracts\Support\Renderable;

class ModuleNotFoundException extends ModuleException implements Renderable
{
    /**
     * @inheritDoc
     */
    public function render()
    {
        return ["result" => false, "message" => "Module not found", "errno" => GlobalErrorCode::MODULE_NOT_FOUND];
    }
}