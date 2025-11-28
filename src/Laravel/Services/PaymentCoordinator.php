<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Laravel\Services;

use CarlLee\NewebPay\Actions\CreditCancel;
use CarlLee\NewebPay\Actions\CreditClose;
use CarlLee\NewebPay\Actions\EWalletRefund;
use CarlLee\NewebPay\FormBuilder;
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

/**
 * 支付協調器。
 *
 * 提供 Laravel 應用程式便捷的藍新金流操作介面。
 */
class PaymentCoordinator
{
    /**
     * 特店編號。
     *
     * @var string
     */
    protected string $merchantID;

    /**
     * HashKey。
     *
     * @var string
     */
    protected string $hashKey;

    /**
     * HashIV。
     *
     * @var string
     */
    protected string $hashIV;

    /**
     * 是否為測試環境。
     *
     * @var bool
     */
    protected bool $isTest;

    /**
     * 建立支付協調器。
     *
     * @param string $merchantId 特店編號
     * @param string $hashKey HashKey
     * @param string $hashIV HashIV
     * @param bool $isTest 是否為測試環境
     */
    public function __construct(
        string $merchantId,
        string $hashKey,
        string $hashIV,
        bool $isTest = false
    ) {
        $this->merchantID = $merchantId;
        $this->hashKey = $hashKey;
        $this->hashIV = $hashIV;
        $this->isTest = $isTest;
    }

    /**
     * 建立快速支付。
     *
     * 提供簡化的支付 API，支援鏈式呼叫。
     *
     * @param string $orderNo 訂單編號
     * @param int $amount 交易金額
     * @param string $itemDesc 商品描述
     * @param string $email 付款人 Email
     * @return PaymentBuilder
     *
     * @example
     * // 基本用法（預設信用卡）
     * return NewebPay::payment($orderNo, $amt, $desc, $email)->submit();
     *
     * // 指定支付方式
     * return NewebPay::payment($orderNo, $amt, $desc, $email)->atm()->submit();
     */
    public function payment(string $orderNo, int $amount, string $itemDesc, string $email = ''): PaymentBuilder
    {
        $builder = new PaymentBuilder(
            $this->merchantID,
            $this->hashKey,
            $this->hashIV,
            $this->isTest
        );

        return $builder->setOrder($orderNo, $amount, $itemDesc, $email);
    }

    /**
     * 建立信用卡一次付清支付。
     *
     * @return CreditPayment
     */
    public function credit(): CreditPayment
    {
        return $this->createPayment(CreditPayment::class);
    }

    /**
     * 建立信用卡分期支付。
     *
     * @return CreditInstallment
     */
    public function creditInstallment(): CreditInstallment
    {
        return $this->createPayment(CreditInstallment::class);
    }

    /**
     * 建立 WebATM 支付。
     *
     * @return WebAtmPayment
     */
    public function webAtm(): WebAtmPayment
    {
        return $this->createPayment(WebAtmPayment::class);
    }

    /**
     * 建立 ATM 轉帳支付。
     *
     * @return AtmPayment
     */
    public function atm(): AtmPayment
    {
        return $this->createPayment(AtmPayment::class);
    }

    /**
     * 建立超商代碼繳費支付。
     *
     * @return CvsPayment
     */
    public function cvs(): CvsPayment
    {
        return $this->createPayment(CvsPayment::class);
    }

    /**
     * 建立超商條碼繳費支付。
     *
     * @return BarcodePayment
     */
    public function barcode(): BarcodePayment
    {
        return $this->createPayment(BarcodePayment::class);
    }

    /**
     * 建立 LINE Pay 支付。
     *
     * @return LinePayPayment
     */
    public function linePay(): LinePayPayment
    {
        return $this->createPayment(LinePayPayment::class);
    }

    /**
     * 建立台灣 Pay 支付。
     *
     * @return TaiwanPayPayment
     */
    public function taiwanPay(): TaiwanPayPayment
    {
        return $this->createPayment(TaiwanPayPayment::class);
    }

    /**
     * 建立玉山 Wallet 支付。
     *
     * @return EsunWalletPayment
     */
    public function esunWallet(): EsunWalletPayment
    {
        return $this->createPayment(EsunWalletPayment::class);
    }

    /**
     * 建立 BitoPay 支付。
     *
     * @return BitoPayPayment
     */
    public function bitoPay(): BitoPayPayment
    {
        return $this->createPayment(BitoPayPayment::class);
    }

    /**
     * 建立超商取貨付款支付。
     *
     * @return CvscomPayment
     */
    public function cvscom(): CvscomPayment
    {
        return $this->createPayment(CvscomPayment::class);
    }

    /**
     * 建立全支付方式。
     *
     * @return AllInOnePayment
     */
    public function allInOne(): AllInOnePayment
    {
        return $this->createPayment(AllInOnePayment::class);
    }

    /**
     * 建立表單產生器。
     *
     * @param mixed $payment 支付操作物件
     * @return FormBuilder
     */
    public function form($payment): FormBuilder
    {
        return new FormBuilder($payment);
    }

    /**
     * 建立交易查詢。
     *
     * @return QueryOrder
     */
    public function queryOrder(): QueryOrder
    {
        return QueryOrder::create($this->merchantID, $this->hashKey, $this->hashIV)
            ->setTestMode($this->isTest);
    }

    /**
     * 建立信用卡明細查詢。
     *
     * @return QueryCreditDetail
     */
    public function queryCreditDetail(): QueryCreditDetail
    {
        return QueryCreditDetail::create($this->merchantID, $this->hashKey, $this->hashIV)
            ->setTestMode($this->isTest);
    }

    /**
     * 建立信用卡取消授權。
     *
     * @return CreditCancel
     */
    public function creditCancel(): CreditCancel
    {
        return CreditCancel::create($this->merchantID, $this->hashKey, $this->hashIV)
            ->setTestMode($this->isTest);
    }

    /**
     * 建立信用卡請退款。
     *
     * @return CreditClose
     */
    public function creditClose(): CreditClose
    {
        return CreditClose::create($this->merchantID, $this->hashKey, $this->hashIV)
            ->setTestMode($this->isTest);
    }

    /**
     * 建立電子錢包退款。
     *
     * @return EWalletRefund
     */
    public function eWalletRefund(): EWalletRefund
    {
        return EWalletRefund::create($this->merchantID, $this->hashKey, $this->hashIV)
            ->setTestMode($this->isTest);
    }

    /**
     * 建立支付操作物件。
     *
     * @template T
     * @param class-string<T> $class 類別名稱
     * @return T
     */
    protected function createPayment(string $class)
    {
        return (new $class($this->merchantID, $this->hashKey, $this->hashIV))
            ->setTestMode($this->isTest);
    }
}
