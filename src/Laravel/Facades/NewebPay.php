<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Laravel\Facades;

use CarlLee\NewebPay\Actions\CreditCancel;
use CarlLee\NewebPay\Actions\CreditClose;
use CarlLee\NewebPay\Actions\EWalletRefund;
use CarlLee\NewebPay\FormBuilder;
use CarlLee\NewebPay\Laravel\Services\PaymentCoordinator;
use CarlLee\NewebPay\Operations\AllInOnePayment;
use CarlLee\NewebPay\Operations\AtmPayment;
use CarlLee\NewebPay\Operations\BarcodePayment;
use CarlLee\NewebPay\Operations\BitoPayPayment;
use CarlLee\NewebPay\Operations\CreditInstallment;
use CarlLee\NewebPay\Operations\CreditPayment;
use CarlLee\NewebPay\Operations\CvscomPayment;
use CarlLee\NewebPay\Operations\CvsPayment;
use CarlLee\NewebPay\Operations\EsunWalletPayment;
use CarlLee\NewebPay\Operations\LinePayPayment;
use CarlLee\NewebPay\Operations\TaiwanPayPayment;
use CarlLee\NewebPay\Operations\WebAtmPayment;
use CarlLee\NewebPay\Queries\QueryCreditDetail;
use CarlLee\NewebPay\Queries\QueryOrder;
use Illuminate\Support\Facades\Facade;

/**
 * 藍新金流 Facade。
 *
 * @method static CreditPayment credit()
 * @method static CreditInstallment creditInstallment()
 * @method static WebAtmPayment webAtm()
 * @method static AtmPayment atm()
 * @method static CvsPayment cvs()
 * @method static BarcodePayment barcode()
 * @method static LinePayPayment linePay()
 * @method static TaiwanPayPayment taiwanPay()
 * @method static EsunWalletPayment esunWallet()
 * @method static BitoPayPayment bitoPay()
 * @method static CvscomPayment cvscom()
 * @method static AllInOnePayment allInOne()
 * @method static FormBuilder form($payment)
 * @method static QueryOrder queryOrder()
 * @method static QueryCreditDetail queryCreditDetail()
 * @method static CreditCancel creditCancel()
 * @method static CreditClose creditClose()
 * @method static EWalletRefund eWalletRefund()
 *
 * @see PaymentCoordinator
 */
class NewebPay extends Facade
{
    /**
     * 取得 Facade 存取器。
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'newebpay';
    }
}
