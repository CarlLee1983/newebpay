<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Laravel\Services;

use CarlLee\NewebPay\Content;
use CarlLee\NewebPay\FormBuilder;
use CarlLee\NewebPay\Operations\AllInOnePayment;
use CarlLee\NewebPay\Operations\AtmPayment;
use CarlLee\NewebPay\Operations\BarcodePayment;
use CarlLee\NewebPay\Operations\CreditInstallment;
use CarlLee\NewebPay\Operations\CreditPayment;
use CarlLee\NewebPay\Operations\CvsPayment;
use CarlLee\NewebPay\Operations\LinePayPayment;
use CarlLee\NewebPay\Operations\TaiwanPayPayment;
use CarlLee\NewebPay\Operations\WebAtmPayment;
use Illuminate\Http\Response;

/**
 * 支付建構器。
 *
 * 提供簡化的支付 API，支援鏈式呼叫。
 *
 * @example
 * // 基本用法（預設信用卡）
 * return NewebPay::payment($orderNo, $amt, $desc, $email)->submit();
 *
 * // 指定支付方式
 * return NewebPay::payment($orderNo, $amt, $desc, $email)->atm()->submit();
 *
 * // 自訂回傳網址
 * return NewebPay::payment($orderNo, $amt, $desc, $email)
 *     ->returnUrl('https://...')
 *     ->notifyUrl('https://...')
 *     ->submit();
 */
class PaymentBuilder
{
    /**
     * 特店編號。
     *
     * @var string
     */
    protected $merchantID;

    /**
     * HashKey。
     *
     * @var string
     */
    protected $hashKey;

    /**
     * HashIV。
     *
     * @var string
     */
    protected $hashIV;

    /**
     * 是否為測試環境。
     *
     * @var bool
     */
    protected $isTest;

    /**
     * 訂單編號。
     *
     * @var string
     */
    protected $orderNo;

    /**
     * 交易金額。
     *
     * @var int
     */
    protected $amount;

    /**
     * 商品描述。
     *
     * @var string
     */
    protected $itemDesc;

    /**
     * 付款人 Email。
     *
     * @var string
     */
    protected $email;

    /**
     * 付款完成返回網址。
     *
     * @var string|null
     */
    protected $returnUrl;

    /**
     * 付款結果通知網址。
     *
     * @var string|null
     */
    protected $notifyUrl;

    /**
     * 取號完成返回網址（ATM/超商）。
     *
     * @var string|null
     */
    protected $customerUrl;

    /**
     * 返回商店網址。
     *
     * @var string|null
     */
    protected $clientBackUrl;

    /**
     * 支付方式類別。
     *
     * @var string
     */
    protected $paymentClass = CreditPayment::class;

    /**
     * 分期期數。
     *
     * @var array|null
     */
    protected $installments;

    /**
     * 繳費期限（ATM/超商）。
     *
     * @var string|null
     */
    protected $expireDate;

    /**
     * 額外設定回呼。
     *
     * @var callable|null
     */
    protected $customizer;

    /**
     * 建立支付建構器。
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
     * 設定基本交易資訊。
     *
     * @param string $orderNo 訂單編號
     * @param int $amount 交易金額
     * @param string $itemDesc 商品描述
     * @param string $email 付款人 Email
     * @return $this
     */
    public function setOrder(string $orderNo, int $amount, string $itemDesc, string $email = ''): self
    {
        $this->orderNo = $orderNo;
        $this->amount = $amount;
        $this->itemDesc = $itemDesc;
        $this->email = $email;

        return $this;
    }

    /**
     * 使用信用卡一次付清。
     *
     * @return $this
     */
    public function creditCard(): self
    {
        $this->paymentClass = CreditPayment::class;

        return $this;
    }

    /**
     * 使用信用卡分期。
     *
     * @param array $periods 分期期數，如 [3, 6, 12]
     * @return $this
     */
    public function creditInstallment(array $periods = [3, 6, 12]): self
    {
        $this->paymentClass = CreditInstallment::class;
        $this->installments = $periods;

        return $this;
    }

    /**
     * 使用 WebATM。
     *
     * @return $this
     */
    public function webAtm(): self
    {
        $this->paymentClass = WebAtmPayment::class;

        return $this;
    }

    /**
     * 使用 ATM 虛擬帳號。
     *
     * @param string|null $expireDate 繳費期限 (Y-m-d)
     * @return $this
     */
    public function atm(?string $expireDate = null): self
    {
        $this->paymentClass = AtmPayment::class;
        $this->expireDate = $expireDate;

        return $this;
    }

    /**
     * 使用超商代碼繳費。
     *
     * @param string|null $expireDate 繳費期限 (Y-m-d)
     * @return $this
     */
    public function cvs(?string $expireDate = null): self
    {
        $this->paymentClass = CvsPayment::class;
        $this->expireDate = $expireDate;

        return $this;
    }

    /**
     * 使用超商條碼繳費。
     *
     * @param string|null $expireDate 繳費期限 (Y-m-d)
     * @return $this
     */
    public function barcode(?string $expireDate = null): self
    {
        $this->paymentClass = BarcodePayment::class;
        $this->expireDate = $expireDate;

        return $this;
    }

    /**
     * 使用 LINE Pay。
     *
     * @return $this
     */
    public function linePay(): self
    {
        $this->paymentClass = LinePayPayment::class;

        return $this;
    }

    /**
     * 使用台灣 Pay。
     *
     * @return $this
     */
    public function taiwanPay(): self
    {
        $this->paymentClass = TaiwanPayPayment::class;

        return $this;
    }

    /**
     * 使用全支付方式（多選）。
     *
     * @return $this
     */
    public function allInOne(): self
    {
        $this->paymentClass = AllInOnePayment::class;

        return $this;
    }

    /**
     * 設定付款完成返回網址。
     *
     * @param string $url 返回網址
     * @return $this
     */
    public function returnUrl(string $url): self
    {
        $this->returnUrl = $url;

        return $this;
    }

    /**
     * 設定付款結果通知網址。
     *
     * @param string $url 通知網址
     * @return $this
     */
    public function notifyUrl(string $url): self
    {
        $this->notifyUrl = $url;

        return $this;
    }

    /**
     * 設定取號完成返回網址（ATM/超商）。
     *
     * @param string $url 返回網址
     * @return $this
     */
    public function customerUrl(string $url): self
    {
        $this->customerUrl = $url;

        return $this;
    }

    /**
     * 設定返回商店網址。
     *
     * @param string $url 返回網址
     * @return $this
     */
    public function clientBackUrl(string $url): self
    {
        $this->clientBackUrl = $url;

        return $this;
    }

    /**
     * 自訂支付物件設定。
     *
     * @param callable $callback 回呼函數，接收 Content 物件
     * @return $this
     */
    public function customize(callable $callback): self
    {
        $this->customizer = $callback;

        return $this;
    }

    /**
     * 建立支付物件。
     *
     * @return Content
     */
    public function build(): Content
    {
        /** @var Content $payment */
        $payment = new $this->paymentClass(
            $this->merchantID,
            $this->hashKey,
            $this->hashIV
        );

        $payment->setTestMode($this->isTest);

        // 設定基本資訊
        $payment->setMerchantOrderNo($this->orderNo);
        $payment->setAmt($this->amount);
        $payment->setItemDesc($this->itemDesc);

        if (!empty($this->email)) {
            $payment->setEmail($this->email);
        }

        // 設定回傳網址
        $returnUrl = $this->returnUrl ?? $this->getConfig('newebpay.return_url');
        $notifyUrl = $this->notifyUrl ?? $this->getConfig('newebpay.notify_url');

        if ($returnUrl) {
            $payment->setReturnURL($returnUrl);
        }

        if ($notifyUrl) {
            $payment->setNotifyURL($notifyUrl);
        }

        if ($this->customerUrl) {
            $payment->setCustomerURL($this->customerUrl);
        }

        if ($this->clientBackUrl) {
            $payment->setClientBackURL($this->clientBackUrl);
        }

        // 設定分期
        if ($payment instanceof CreditInstallment && $this->installments) {
            $payment->setInstallment($this->installments);
        }

        // 設定繳費期限
        if ($this->expireDate && method_exists($payment, 'setExpireDate')) {
            $payment->setExpireDate($this->expireDate);
        }

        // 執行自訂設定
        if ($this->customizer) {
            call_user_func($this->customizer, $payment);
        }

        return $payment;
    }

    /**
     * 從 Laravel config 取得設定值。
     *
     * @param string $key 設定鍵
     * @param mixed $default 預設值
     * @return mixed
     */
    protected function getConfig(string $key, $default = null)
    {
        // 檢查是否在 Laravel 應用程式環境中
        if (function_exists('app') && app()->bound('config')) {
            return config($key, $default);
        }

        return $default;
    }

    /**
     * 取得表單 HTML。
     *
     * @param bool $autoSubmit 是否自動送出
     * @return string
     */
    public function getHtml(bool $autoSubmit = true): string
    {
        $payment = $this->build();

        return (new FormBuilder($payment))
            ->setAutoSubmit($autoSubmit)
            ->build();
    }

    /**
     * 送出支付請求。
     *
     * 回傳包含自動提交表單的 HTTP Response。
     *
     * @return Response
     */
    public function submit(): Response
    {
        $html = $this->getHtml(true);

        return new Response($html);
    }

    /**
     * 取得支付參數（供前端 AJAX 使用）。
     *
     * @return array
     */
    public function getParams(): array
    {
        $payment = $this->build();

        return [
            'action' => $payment->getApiUrl(),
            'method' => 'POST',
            'fields' => $payment->getContent(),
        ];
    }
}
