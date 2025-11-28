<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Operations;

use CarlLee\NewebPay\Content;
use CarlLee\NewebPay\Exceptions\NewebPayException;
use CarlLee\NewebPay\Parameter\BankType;
use Override;

/**
 * ATM 轉帳支付（虛擬帳號）。
 *
 * 產生虛擬帳號供消費者至 ATM 或網路銀行轉帳。
 */
class AtmPayment extends Content
{
    /**
     * 支援的銀行（向後相容常數）。
     */
    public const string BANK_BOT = 'BOT';
    public const string BANK_HNCB = 'HNCB';
    public const string BANK_FIRST = 'FIRST';

    /**
     * @inheritDoc
     */
    #[Override]
    protected function initContent(): void
    {
        parent::initContent();

        // 啟用 ATM 轉帳
        $this->content['VACC'] = 1;
    }

    /**
     * 設定指定付款銀行。
     *
     * @param string|BankType $bank 銀行代碼或 BankType 列舉
     * @return static
     */
    public function setBankType(string|BankType $bank): static
    {
        $bankValue = $bank instanceof BankType ? $bank->value : $bank;

        $validBanks = [self::BANK_BOT, self::BANK_HNCB, self::BANK_FIRST];

        if (!in_array($bankValue, $validBanks, true)) {
            throw NewebPayException::invalid('BankType', '銀行代碼必須為 BOT, HNCB 或 FIRST');
        }

        $this->content['BankType'] = $bankValue;

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    protected function validation(): void
    {
        $this->validateBaseParams();
    }
}
