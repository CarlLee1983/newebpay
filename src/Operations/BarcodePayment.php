<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Operations;

use CarlLee\NewebPay\Content;
use CarlLee\NewebPay\Exceptions\NewebPayException;
use Override;

/**
 * 超商條碼繳費支付。
 *
 * 產生繳費條碼供消費者至超商繳費。
 * 金額限制：20~40,000 元。
 */
class BarcodePayment extends Content
{
    /**
     * 最小金額。
     */
    public const int MIN_AMOUNT = 20;

    /**
     * 最大金額。
     */
    public const int MAX_AMOUNT = 40000;

    /**
     * @inheritDoc
     */
    #[Override]
    protected function initContent(): void
    {
        parent::initContent();

        // 啟用超商條碼繳費
        $this->content['BARCODE'] = 1;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function setAmt(int $amount): static
    {
        if ($amount < self::MIN_AMOUNT || $amount > self::MAX_AMOUNT) {
            throw NewebPayException::invalid(
                'Amt',
                sprintf('超商條碼繳費金額必須在 %d~%d 元之間', self::MIN_AMOUNT, self::MAX_AMOUNT)
            );
        }

        return parent::setAmt($amount);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    protected function validation(): void
    {
        $this->validateBaseParams();

        // 驗證金額範圍
        $amt = $this->content['Amt'] ?? 0;
        if ($amt < self::MIN_AMOUNT || $amt > self::MAX_AMOUNT) {
            throw NewebPayException::invalid(
                'Amt',
                sprintf('超商條碼繳費金額必須在 %d~%d 元之間', self::MIN_AMOUNT, self::MAX_AMOUNT)
            );
        }
    }
}
