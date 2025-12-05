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
    private string $orderNo = '';
    private int $amount = 0;
    private string $itemDesc = '';
    private string $email = '';
    private ?string $returnUrl = null;
    private ?string $notifyUrl = null;
    private ?string $customerUrl = null;
    private ?string $clientBackUrl = null;

    /** @var class-string<Content> */
    private string $paymentClass = CreditPayment::class;

    /** @var array<int>|null */
    private ?array $installments = null;

    private ?string $expireDate = null;

    /** @var callable|null */
    private $customizer = null;

    /**
     * 建立支付建構器。
     */
    public function __construct(
        private readonly string $merchantID,
        private readonly string $hashKey,
        private readonly string $hashIV,
        private readonly bool $isTest = false
    ) {
    }

    /**
     * 設定基本交易資訊。
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
     */
    public function creditCard(): self
    {
        $this->paymentClass = CreditPayment::class;

        return $this;
    }

    /**
     * 使用信用卡分期。
     *
     * @param array<int> $periods 分期期數，如 [3, 6, 12]
     */
    public function creditInstallment(array $periods = [3, 6, 12]): self
    {
        $this->paymentClass = CreditInstallment::class;
        $this->installments = $periods;

        return $this;
    }

    /**
     * 使用 WebATM。
     */
    public function webAtm(): self
    {
        $this->paymentClass = WebAtmPayment::class;

        return $this;
    }

    /**
     * 使用 ATM 虛擬帳號。
     */
    public function atm(?string $expireDate = null): self
    {
        $this->paymentClass = AtmPayment::class;
        $this->expireDate = $expireDate;

        return $this;
    }

    /**
     * 使用超商代碼繳費。
     */
    public function cvs(?string $expireDate = null): self
    {
        $this->paymentClass = CvsPayment::class;
        $this->expireDate = $expireDate;

        return $this;
    }

    /**
     * 使用超商條碼繳費。
     */
    public function barcode(?string $expireDate = null): self
    {
        $this->paymentClass = BarcodePayment::class;
        $this->expireDate = $expireDate;

        return $this;
    }

    /**
     * 使用 LINE Pay。
     */
    public function linePay(): self
    {
        $this->paymentClass = LinePayPayment::class;

        return $this;
    }

    /**
     * 使用台灣 Pay。
     */
    public function taiwanPay(): self
    {
        $this->paymentClass = TaiwanPayPayment::class;

        return $this;
    }

    /**
     * 使用全支付方式（多選）。
     */
    public function allInOne(): self
    {
        $this->paymentClass = AllInOnePayment::class;

        return $this;
    }

    /**
     * 設定付款完成返回網址。
     */
    public function returnUrl(string $url): self
    {
        $this->returnUrl = $url;

        return $this;
    }

    /**
     * 設定付款結果通知網址。
     */
    public function notifyUrl(string $url): self
    {
        $this->notifyUrl = $url;

        return $this;
    }

    /**
     * 設定取號完成返回網址（ATM/超商）。
     */
    public function customerUrl(string $url): self
    {
        $this->customerUrl = $url;

        return $this;
    }

    /**
     * 設定返回商店網址。
     */
    public function clientBackUrl(string $url): self
    {
        $this->clientBackUrl = $url;

        return $this;
    }

    /**
     * 自訂支付物件設定。
     */
    public function customize(callable $callback): self
    {
        $this->customizer = $callback;

        return $this;
    }

    /**
     * 建立支付物件。
     */
    public function build(): Content
    {
        /** @var Content $payment */
        $payment = new ($this->paymentClass)(
            $this->merchantID,
            $this->hashKey,
            $this->hashIV
        );

        $payment->setTestMode($this->isTest);

        // 設定基本資訊
        $payment->setMerchantOrderNo($this->orderNo);
        $payment->setAmt($this->amount);
        $payment->setItemDesc($this->itemDesc);

        if ($this->email !== '') {
            $payment->setEmail($this->email);
        }

        // 設定回傳網址
        $returnUrl = $this->returnUrl ?? $this->getConfig('newebpay.return_url');
        $notifyUrl = $this->notifyUrl ?? $this->getConfig('newebpay.notify_url');

        if ($returnUrl !== null) {
            $payment->setReturnURL($returnUrl);
        }

        if ($notifyUrl !== null) {
            $payment->setNotifyURL($notifyUrl);
        }

        if ($this->customerUrl !== null) {
            $payment->setCustomerURL($this->customerUrl);
        }

        if ($this->clientBackUrl !== null) {
            $payment->setClientBackURL($this->clientBackUrl);
        }

        // 設定分期
        if ($payment instanceof CreditInstallment && $this->installments !== null) {
            $payment->setInstallment($this->installments);
        }

        // 設定繳費期限
        if ($this->expireDate !== null) {
            $payment->setExpireDate($this->expireDate);
        }

        // 執行自訂設定
        if ($this->customizer !== null) {
            ($this->customizer)($payment);
        }

        return $payment;
    }

    /**
     * 從 Laravel config 取得設定值。
     */
    private function getConfig(string $key, mixed $default = null): mixed
    {
        // 檢查是否在 Laravel 應用程式環境中
        if (function_exists('app') && app()->bound('config')) {
            return config($key, $default);
        }

        return $default;
    }

    /**
     * 取得表單 HTML。
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
     */
    public function submit(): Response
    {
        $html = $this->getHtml(true);

        return new Response($html);
    }

    /**
     * 取得支付參數（供前端 AJAX 使用）。
     *
     * @return array{action: string, method: string, fields: array<string, mixed>}
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
