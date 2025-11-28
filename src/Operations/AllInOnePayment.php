<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Operations;

use CarlLee\NewebPay\Content;
use CarlLee\NewebPay\Exceptions\NewebPayException;

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
    public function enableCredit(int $enable = 1): self
    {
        $this->content['CREDIT'] = $enable;

        return $this;
    }

    /**
     * 啟用信用卡分期。
     *
     * @param array<int>|string $installments 期數
     * @return static
     */
    public function enableInstallment($installments): self
    {
        $this->content['CREDIT'] = 1;

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
    public function enableWebAtm(int $enable = 1): self
    {
        $this->content['WEBATM'] = $enable;

        return $this;
    }

    /**
     * 啟用 ATM 轉帳。
     *
     * @param int $enable 0=不啟用, 1=啟用
     * @return static
     */
    public function enableAtm(int $enable = 1): self
    {
        $this->content['VACC'] = $enable;

        return $this;
    }

    /**
     * 啟用超商代碼繳費。
     *
     * @param int $enable 0=不啟用, 1=啟用
     * @return static
     */
    public function enableCvs(int $enable = 1): self
    {
        $this->content['CVS'] = $enable;

        return $this;
    }

    /**
     * 啟用超商條碼繳費。
     *
     * @param int $enable 0=不啟用, 1=啟用
     * @return static
     */
    public function enableBarcode(int $enable = 1): self
    {
        $this->content['BARCODE'] = $enable;

        return $this;
    }

    /**
     * 啟用 LINE Pay。
     *
     * @param int $enable 0=不啟用, 1=啟用
     * @return static
     */
    public function enableLinePay(int $enable = 1): self
    {
        $this->content['LINEPAY'] = $enable;

        return $this;
    }

    /**
     * 啟用台灣 Pay。
     *
     * @param int $enable 0=不啟用, 1=啟用
     * @return static
     */
    public function enableTaiwanPay(int $enable = 1): self
    {
        $this->content['TAIWANPAY'] = $enable;

        return $this;
    }

    /**
     * 啟用玉山 Wallet。
     *
     * @param int $enable 0=不啟用, 1=啟用
     * @return static
     */
    public function enableEsunWallet(int $enable = 1): self
    {
        $this->content['ESUNWALLET'] = $enable;

        return $this;
    }

    /**
     * 啟用 BitoPay。
     *
     * @param int $enable 0=不啟用, 1=啟用
     * @return static
     */
    public function enableBitoPay(int $enable = 1): self
    {
        $this->content['BITOPAY'] = $enable;

        return $this;
    }

    /**
     * 啟用超商取貨付款。
     *
     * @param int $enable 0=不啟用, 1=啟用
     * @return static
     */
    public function enableCvscom(int $enable = 1): self
    {
        $this->content['CVSCOM'] = $enable;

        return $this;
    }

    /**
     * 啟用所有支付方式。
     *
     * @return static
     */
    public function enableAll(): self
    {
        $this->content['CREDIT'] = 1;
        $this->content['WEBATM'] = 1;
        $this->content['VACC'] = 1;
        $this->content['CVS'] = 1;
        $this->content['BARCODE'] = 1;
        $this->content['LINEPAY'] = 1;
        $this->content['TAIWANPAY'] = 1;
        $this->content['ESUNWALLET'] = 1;
        $this->content['BITOPAY'] = 1;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function validation(): void
    {
        $this->validateBaseParams();

        // 檢查至少啟用一種支付方式
        $paymentMethods = [
            'CREDIT', 'WEBATM', 'VACC', 'CVS', 'BARCODE',
            'LINEPAY', 'TAIWANPAY', 'ESUNWALLET', 'BITOPAY', 'CVSCOM',
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
