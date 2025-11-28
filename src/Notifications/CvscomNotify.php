<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Notifications;

/**
 * 超商取貨付款（物流）通知處理。
 *
 * 用於處理藍新金流回傳的物流相關通知。
 */
class CvscomNotify extends PaymentNotify
{
    /**
     * 取得超商類型。
     *
     * @return string
     */
    public function getStoreType(): string
    {
        $result = $this->getResult();

        return (string) ($result['StoreType'] ?? '');
    }

    /**
     * 取得超商門市代號。
     *
     * @return string
     */
    public function getStoreID(): string
    {
        $result = $this->getResult();

        return (string) ($result['StoreID'] ?? '');
    }

    /**
     * 取得寄件編號。
     *
     * @return string
     */
    public function getCvsNo(): string
    {
        $result = $this->getResult();

        return (string) ($result['CVSNo'] ?? '');
    }

    /**
     * 取得收件人姓名。
     *
     * @return string
     */
    public function getReceiverName(): string
    {
        $result = $this->getResult();

        return (string) ($result['ReceiverName'] ?? '');
    }

    /**
     * 取得收件人電話。
     *
     * @return string
     */
    public function getReceiverPhone(): string
    {
        $result = $this->getResult();

        return (string) ($result['ReceiverPhone'] ?? '');
    }

    /**
     * 取得收件人地址。
     *
     * @return string
     */
    public function getReceiverAddress(): string
    {
        $result = $this->getResult();

        return (string) ($result['ReceiverAddress'] ?? '');
    }
}
