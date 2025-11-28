<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Operations;

use CarlLee\NewebPay\Content;
use CarlLee\NewebPay\Exceptions\NewebPayException;
use CarlLee\NewebPay\Parameter\LgsType;

/**
 * 超商取貨付款支付。
 *
 * 支援超商取貨付款物流服務。
 * 金額限制：30~20,000 元。
 */
class CvscomPayment extends Content
{
    /**
     * 最小金額。
     */
    public const MIN_AMOUNT = 30;

    /**
     * 最大金額。
     */
    public const MAX_AMOUNT = 20000;

    /**
     * @inheritDoc
     */
    protected function initContent(): void
    {
        parent::initContent();

        // 啟用超商取貨付款
        $this->content['CVSCOM'] = 1;
    }

    /**
     * 設定物流類型。
     *
     * @param string $type 物流類型 (FAMILY, UNIMART, HILIFE, OKMART)
     * @return static
     */
    public function setLgsType(string $type): self
    {
        $validTypes = LgsType::all();

        if (!in_array($type, $validTypes, true)) {
            throw NewebPayException::invalid('LgsType', '物流類型必須為 FAMILY, UNIMART, HILIFE 或 OKMART');
        }

        $this->content['LgsType'] = $type;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setAmt(int $amount): self
    {
        if ($amount < self::MIN_AMOUNT || $amount > self::MAX_AMOUNT) {
            throw NewebPayException::invalid(
                'Amt',
                sprintf('超商取貨付款金額必須在 %d~%d 元之間', self::MIN_AMOUNT, self::MAX_AMOUNT)
            );
        }

        return parent::setAmt($amount);
    }

    /**
     * @inheritDoc
     */
    protected function validation(): void
    {
        $this->validateBaseParams();

        // 驗證金額範圍
        $amt = $this->content['Amt'] ?? 0;
        if ($amt < self::MIN_AMOUNT || $amt > self::MAX_AMOUNT) {
            throw NewebPayException::invalid(
                'Amt',
                sprintf('超商取貨付款金額必須在 %d~%d 元之間', self::MIN_AMOUNT, self::MAX_AMOUNT)
            );
        }
    }
}
