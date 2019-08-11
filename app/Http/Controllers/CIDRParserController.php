<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use YunInternet\PHPIPCalculator\CalculatorFactory;
use YunInternet\PHPIPCalculator\Constants;
use YunInternet\PHPIPCalculator\Exception\Exception;

class CIDRParserController extends Controller
{
    public function __invoke(Request $request)
    {
        $calculatorFactory = new CalculatorFactory($request->cidr);
        try {
            $calculator = $calculatorFactory->create();
            $parsedResult = [
                "result" => true,
                "type" => $calculator->getType(),
            ];

            if ($calculator->getType() === Constants::TYPE_IPV4 && $request->autoRemoveUnusableAddress) {
                $parsedResult["networkBits"] = $calculator->getNetworkBits();
                $parsedResult["gateway"] = $calculator::calculable2HumanReadable($calculator->ipAt(1));
                $parsedResult["first"] = $calculator::calculable2HumanReadable($calculator->ipAt(2));
                $parsedResult["last"] = $calculator::calculable2HumanReadable($calculator->ipReverseAt(1));
            } else {
                $parsedResult["first"] = $calculator->getFirstHumanReadableAddress();
                $parsedResult["last"] = $calculator->getLastHumanReadableAddress();
            }

            return [
                "result" => true,
                "type" => $calculator->getType(),
                "parsed" => $parsedResult,
            ];
        } catch (Exception $e) {
            return ["result" => false, "errno" => $e->getCode(), "message" => $e->getMessage()];
        }
    }
}
