<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-24
 * Time: 下午2:32
 */

namespace App\Exceptions;


use App\Constants\GlobalErrorCode;
use Illuminate\Contracts\Support\Renderable;
use Throwable;

class MinCreditRequired extends \Exception implements Renderable
{
    private $min;

    private $current;

    public function __construct($min, $current, string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->min = $min;
        $this->current = $current;
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        return ["result" => false, "errno" => GlobalErrorCode::MIN_CREDIT_REQUIRED, "min" => $this->min, "current" => $this->current];
    }
}