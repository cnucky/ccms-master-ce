<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-5
 * Time: 下午2:54
 */

namespace App\Module\Contract;


use App\Module\Exception\CancelRefundException;
use App\Module\Exception\ModuleException;
use App\Module\Resource\Payment\FormField;
use App\Module\Resource\Payment\PayResponse;
use App\PaymentTrade;
use App\PaymentTradeRefund;
use Illuminate\Http\Request;

interface PaymentModule
{
    public function __construct(Logger $logger, $moduleSettings, $currencyCode);

    /**
     * @param Request $request
     * @param PaymentTrade $paymentTrade
     * @param string $notifyURL
     * @param string $returnURL
     * @return string|array|PayResponse
     * @throws ModuleException
     */
    public function pay(Request $request, PaymentTrade $paymentTrade, $notifyURL, $returnURL);

    /**
     * @param Request $request
     * @param PaymentTradeRefund $paymentTradeRefund
     * @param string $notifyURL
     * @return true|string|null On refunded successfully, return true or platform transaction no, null if need notify
     * @throws ModuleException
     * @throws CancelRefundException Throw this exception if refund request can be cancelled securely
     */
    public function refund(Request $request, PaymentTradeRefund $paymentTradeRefund, $notifyURL);

    /**
     * @return string
     */
    public static function getName() : string;

    /**
     * @return string
     */
    public static function getVersion() : string;

    /**
     * @return string
     */
    public static function getDescription() : string;

    /**
     * @return FormField[]
     */
    public static function getAvailableSettings() : array;

    /**
     * @return null|array
     */
    public static function getAvailableChannels();
}