<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-7
 * Time: 下午3:15
 */

namespace App\Module\Resource;

/**
 * Class FeeProcessor
 * Processing without any input validation!
 * @package App\Module\Resource
 */
class FeeProcessor
{
    /**
     * @param string $fee
     * @return string
     */
    public static function multiply100($fee)
    {
        @list($left, $right) = explode(".", $fee);
        if (is_null($right) || $right === "") {
            $processedFee = $left . "00";
        } else if (strlen($right) === 1) {
            $processedFee = $left . $right . "0";
        } else {
            $processedFee = $left . $right;
        }

        $processedFee = ltrim($processedFee, "0");
        if ($processedFee === "")
            return "0";
        return $processedFee;
    }

    /**
     * @param string $fee Only positive decimal is accepted
     * @return string
     */
    public static function divide100($fee)
    {
        $feeLength = strlen($fee);
        if ($feeLength > 2) {
            $left = substr($fee, 0, $feeLength - 2);
            $right = substr($fee, $feeLength - 2);
            return $left . "." . $right;
        }
        if ($feeLength == 2) {
            return "0." . $fee;
        }
        return "0.0" . $fee;
    }
}