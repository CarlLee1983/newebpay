<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Notifications;

/**
 * 超商取號完成通知處理器。
 *
 * 處理超商代碼/條碼取號完成的回傳通知。
 */
class CvsNotify extends PaymentNotify
{
    /**
     * 取得繳費代碼。
     *
     * @return string
     */
    public function getCodeNo(): string
    {
        $result = $this->getData()['Result'] ?? [];

        return (string) ($result['CodeNo'] ?? '');
    }

    /**
     * 取得超商類型。
     *
     * @return string
     */
    public function getStoreType(): string
    {
        $result = $this->getData()['Result'] ?? [];

        return (string) ($result['StoreType'] ?? '');
    }

    /**
     * 取得條碼第一段。
     *
     * @return string
     */
    public function getBarcode1(): string
    {
        $result = $this->getData()['Result'] ?? [];

        return (string) ($result['Barcode_1'] ?? '');
    }

    /**
     * 取得條碼第二段。
     *
     * @return string
     */
    public function getBarcode2(): string
    {
        $result = $this->getData()['Result'] ?? [];

        return (string) ($result['Barcode_2'] ?? '');
    }

    /**
     * 取得條碼第三段。
     *
     * @return string
     */
    public function getBarcode3(): string
    {
        $result = $this->getData()['Result'] ?? [];

        return (string) ($result['Barcode_3'] ?? '');
    }

    /**
     * 取得繳費截止日期。
     *
     * @return string
     */
    public function getExpireDate(): string
    {
        $result = $this->getData()['Result'] ?? [];

        return (string) ($result['ExpireDate'] ?? '');
    }

    /**
     * 取得繳費截止時間。
     *
     * @return string
     */
    public function getExpireTime(): string
    {
        $result = $this->getData()['Result'] ?? [];

        return (string) ($result['ExpireTime'] ?? '');
    }
}
