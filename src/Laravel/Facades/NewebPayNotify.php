<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Laravel\Facades;

use CarlLee\NewebPay\Notifications\PaymentNotify;
use Illuminate\Support\Facades\Facade;

/**
 * 藍新金流通知處理 Facade。
 *
 * @method static bool verify(array $data)
 * @method static PaymentNotify verifyOrFail(array $data)
 * @method static array getData()
 * @method static array getRawData()
 * @method static bool isSuccess()
 * @method static string getStatus()
 * @method static string getMessage()
 * @method static string getMerchantID()
 * @method static string getMerchantOrderNo()
 * @method static string getTradeNo()
 * @method static int getAmt()
 * @method static string getPaymentType()
 * @method static string getPayTime()
 * @method static string getIP()
 * @method static string getPayBankCode()
 * @method static string getAuthCode()
 * @method static string getCard4No()
 * @method static string getCard6No()
 * @method static string getECI()
 * @method static int getInst()
 * @method static int getInstFirst()
 * @method static int getInstEach()
 * @method static array getResult()
 * @method static bool isVerified()
 *
 * @see PaymentNotify
 */
class NewebPayNotify extends Facade
{
    /**
     * 取得 Facade 存取器。
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'newebpay.notify';
    }
}
