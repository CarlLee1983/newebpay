<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Operations;

use CarlLee\NewebPay\Content;
use CarlLee\NewebPay\Exceptions\NewebPayException;

/**
 * ATM 轉帳支付（虛擬帳號）。
 *
 * 產生虛擬帳號供消費者至 ATM 或網路銀行轉帳。
 */
class AtmPayment extends Content
{
    /**
     * 支援的銀行。
     */
    public const BANK_BOT = 'BOT';       // 台灣銀行
    public const BANK_HNCB = 'HNCB';     // 華南銀行
    public const BANK_FIRST = 'FIRST';   // 第一銀行

    /**
     * @inheritDoc
     */
    protected function initContent(): void
    {
        parent::initContent();

        // 啟用 ATM 轉帳
        $this->content['VACC'] = 1;
    }

    /**
     * 設定指定付款銀行。
     *
     * @param string $bank 銀行代碼 (BOT, HNCB, FIRST)
     * @return static
     */
    public function setBankType(string $bank): self
    {
        $validBanks = [self::BANK_BOT, self::BANK_HNCB, self::BANK_FIRST];

        if (!in_array($bank, $validBanks, true)) {
            throw NewebPayException::invalid('BankType', '銀行代碼必須為 BOT, HNCB 或 FIRST');
        }

        $this->content['BankType'] = $bank;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function validation(): void
    {
        $this->validateBaseParams();
    }
}
