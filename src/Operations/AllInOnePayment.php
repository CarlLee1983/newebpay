<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Operations;

use CarlLee\NewebPay\Content;
use CarlLee\NewebPay\Exceptions\NewebPayException;
use Override;

/**
 * 全支付方式。
 *
 * 允許消費者選擇所有可用的支付方式。
 */
class AllInOnePayment extends Content
{
    /**
     * @inheritDoc
     */
    #[Override]
    protected function initContent(): void
    {
        parent::initContent();

        // 預設不啟用任何支付方式，需手動設定
    }

    /**
     * 啟用信用卡。
     *
     * @param int $enable 0=不啟用, 1=啟用
     * @return static
     */
    public function enableCredit(int $enable = 1): static
    {
        $this->content[\CarlLee\NewebPay\Parameter\PaymentType::Credit->value] = $enable;

        return $this;
    }

    /**
     * 啟用信用卡分期。
     *
     * @param array<int>|string $installments 期數
     * @return static
     */
    public function enableInstallment(array|string $installments): static
    {
        $this->content[\CarlLee\NewebPay\Parameter\PaymentType::Credit->value] = 1;

        if (is_array($installments)) {
            $this->content['InstFlag'] = implode(',', $installments);
        } else {
            $this->content['InstFlag'] = $installments;
        }

        return $this;
    }

    /**
     * 啟用 WebATM。
     *
     * @param int $enable 0=不啟用, 1=啟用
     * @return static
     */
    public function enableWebAtm(int $enable = 1): static
    {
        $this->content[\CarlLee\NewebPay\Parameter\PaymentType::WebAtm->value] = $enable;

        return $this;
    }

    /**
     * 啟用 ATM 轉帳。
     *
     * @param int $enable 0=不啟用, 1=啟用
     * @return static
     */
    public function enableAtm(int $enable = 1): static
    {
        $this->content[\CarlLee\NewebPay\Parameter\PaymentType::Vacc->value] = $enable;

        return $this;
    }

    /**
     * 啟用超商代碼繳費。
     *
     * @param int $enable 0=不啟用, 1=啟用
     * @return static
     */
    public function enableCvs(int $enable = 1): static
    {
        $this->content[\CarlLee\NewebPay\Parameter\PaymentType::Cvs->value] = $enable;

        return $this;
    }

    /**
     * 啟用超商條碼繳費。
     *
     * @param int $enable 0=不啟用, 1=啟用
     * @return static
     */
    public function enableBarcode(int $enable = 1): static
    {
        $this->content[\CarlLee\NewebPay\Parameter\PaymentType::Barcode->value] = $enable;

        return $this;
    }

    /**
     * 啟用 LINE Pay。
     *
     * @param int $enable 0=不啟用, 1=啟用
     * @return static
     */
    public function enableLinePay(int $enable = 1): static
    {
        $this->content[\CarlLee\NewebPay\Parameter\PaymentType::LinePay->value] = $enable;

        return $this;
    }

    /**
     * 啟用台灣 Pay。
     *
     * @param int $enable 0=不啟用, 1=啟用
     * @return static
     */
    public function enableTaiwanPay(int $enable = 1): static
    {
        $this->content[\CarlLee\NewebPay\Parameter\PaymentType::TaiwanPay->value] = $enable;

        return $this;
    }

    /**
     * 啟用玉山 Wallet。
     *
     * @param int $enable 0=不啟用, 1=啟用
     * @return static
     */
    public function enableEsunWallet(int $enable = 1): static
    {
        $this->content[\CarlLee\NewebPay\Parameter\PaymentType::EsunWallet->value] = $enable;

        return $this;
    }

    /**
     * 啟用 BitoPay。
     *
     * @param int $enable 0=不啟用, 1=啟用
     * @return static
     */
    public function enableBitoPay(int $enable = 1): static
    {
        $this->content[\CarlLee\NewebPay\Parameter\PaymentType::BitoPay->value] = $enable;

        return $this;
    }

    /**
     * 啟用超商取貨付款。
     *
     * @param int $enable 0=不啟用, 1=啟用
     * @return static
     */
    public function enableCvscom(int $enable = 1): static
    {
        $this->content[\CarlLee\NewebPay\Parameter\PaymentType::Cvscom->value] = $enable;

        return $this;
    }

    /**
     * 啟用所有支付方式。
     *
     * @return static
     */
    public function enableAll(): static
    {
        $this->content[\CarlLee\NewebPay\Parameter\PaymentType::Credit->value] = 1;
        $this->content[\CarlLee\NewebPay\Parameter\PaymentType::WebAtm->value] = 1;
        $this->content[\CarlLee\NewebPay\Parameter\PaymentType::Vacc->value] = 1;
        $this->content[\CarlLee\NewebPay\Parameter\PaymentType::Cvs->value] = 1;
        $this->content[\CarlLee\NewebPay\Parameter\PaymentType::Barcode->value] = 1;
        $this->content[\CarlLee\NewebPay\Parameter\PaymentType::LinePay->value] = 1;
        $this->content[\CarlLee\NewebPay\Parameter\PaymentType::TaiwanPay->value] = 1;
        $this->content[\CarlLee\NewebPay\Parameter\PaymentType::EsunWallet->value] = 1;
        $this->content[\CarlLee\NewebPay\Parameter\PaymentType::BitoPay->value] = 1;

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    protected function validation(): void
    {
        $this->validateBaseParams();

        // 檢查至少啟用一種支付方式
        $paymentMethods = [
            \CarlLee\NewebPay\Parameter\PaymentType::Credit->value,
            \CarlLee\NewebPay\Parameter\PaymentType::WebAtm->value,
            \CarlLee\NewebPay\Parameter\PaymentType::Vacc->value,
            \CarlLee\NewebPay\Parameter\PaymentType::Cvs->value,
            \CarlLee\NewebPay\Parameter\PaymentType::Barcode->value,
            \CarlLee\NewebPay\Parameter\PaymentType::LinePay->value,
            \CarlLee\NewebPay\Parameter\PaymentType::TaiwanPay->value,
            \CarlLee\NewebPay\Parameter\PaymentType::EsunWallet->value,
            \CarlLee\NewebPay\Parameter\PaymentType::BitoPay->value,
            \CarlLee\NewebPay\Parameter\PaymentType::Cvscom->value,
        ];

        $hasPayment = false;
        foreach ($paymentMethods as $method) {
            if (!empty($this->content[$method])) {
                $hasPayment = true;
                break;
            }
        }

        if (!$hasPayment) {
            throw NewebPayException::invalid('PaymentMethod', '至少需啟用一種支付方式');
        }
    }
}
