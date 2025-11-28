<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Notifications;

/**
 * ATM 取號完成通知處理器。
 *
 * 處理 ATM 轉帳虛擬帳號取號完成的回傳通知。
 */
class AtmNotify extends PaymentNotify
{
    /**
     * 取得銀行代碼。
     *
     * @return string
     */
    public function getBankCode(): string
    {
        $result = $this->getData()['Result'] ?? [];

        return (string) ($result['BankCode'] ?? '');
    }

    /**
     * 取得虛擬帳號。
     *
     * @return string
     */
    public function getCodeNo(): string
    {
        $result = $this->getData()['Result'] ?? [];

        return (string) ($result['CodeNo'] ?? '');
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
