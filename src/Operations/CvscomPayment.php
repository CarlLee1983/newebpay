<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Operations;

use CarlLee\NewebPay\Content;
use CarlLee\NewebPay\Exceptions\NewebPayException;
use CarlLee\NewebPay\Parameter\LgsType;
use Override;

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
    public const int MIN_AMOUNT = 30;

    /**
     * 最大金額。
     */
    public const int MAX_AMOUNT = 20000;

    /**
     * @inheritDoc
     */
    #[Override]
    protected function initContent(): void
    {
        parent::initContent();

        // 啟用超商取貨付款
        $this->content['CVSCOM'] = 1;
    }

    /**
     * 設定物流類型。
     *
     * @param string|LgsType $type 物流類型或 LgsType 列舉
     * @return static
     */
    public function setLgsType(string|LgsType $type): static
    {
        $typeValue = $type instanceof LgsType ? $type->value : $type;

        $validTypes = LgsType::values();

        if (!in_array($typeValue, $validTypes, true)) {
            throw NewebPayException::invalid('LgsType', '物流類型必須為 FAMILY, UNIMART, HILIFE 或 OKMART');
        }

        $this->content['LgsType'] = $typeValue;

        return $this;
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
                sprintf('超商取貨付款金額必須在 %d~%d 元之間', self::MIN_AMOUNT, self::MAX_AMOUNT)
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
                sprintf('超商取貨付款金額必須在 %d~%d 元之間', self::MIN_AMOUNT, self::MAX_AMOUNT)
            );
        }
    }
}
