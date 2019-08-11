<?php
/**
 * Created by PhpStorm.
 * Date: 19-2-1
 * Time: 下午11:48
 */

namespace App\Utils\Node\ComputeNode\Exception;


use Throwable;

class Exception extends \Exception
{
    private $response;

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null, $response = null)
    {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }
}