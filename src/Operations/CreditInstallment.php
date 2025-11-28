<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Operations;

use CarlLee\NewebPay\Content;
use CarlLee\NewebPay\Exceptions\NewebPayException;

/**
 * 信用卡分期付款。
 *
 * 支援 3, 6, 12, 18, 24, 30 期分期。
 */
class CreditInstallment extends Content
{
    /**
     * 有效的分期期數。
     *
     * @var array<int>
     */
    private const VALID_INSTALLMENTS = [3, 6, 12, 18, 24, 30];

    /**
     * @inheritDoc
     */
    protected function initContent(): void
    {
        parent::initContent();

        // 啟用信用卡分期
        $this->content['CREDIT'] = 1;
    }

    /**
     * 設定分期期數。
     *
     * 可設定多個期數供消費者選擇。
     *
     * @param array<int>|int $installments 期數（可為單一值或陣列）
     * @return static
     * @throws NewebPayException 當期數無效時
     */
    public function setInstallment($installments): self
    {
        if (!is_array($installments)) {
            $installments = [$installments];
        }

        foreach ($installments as $inst) {
            if (!in_array((int) $inst, self::VALID_INSTALLMENTS, true)) {
                throw NewebPayException::invalid('InstFlag', '期數必須為 3, 6, 12, 18, 24 或 30');
            }
        }

        $this->content['InstFlag'] = implode(',', $installments);

        return $this;
    }

    /**
     * 設定是否啟用紅利折抵。
     *
     * @param int $enable 0=不啟用, 1=啟用
     * @return static
     */
    public function setRedeem(int $enable): self
    {
        $this->content['CreditRed'] = $enable;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function validation(): void
    {
        $this->validateBaseParams();

        // 驗證必須設定分期期數
        if (empty($this->content['InstFlag'])) {
            throw NewebPayException::required('InstFlag');
        }
    }
}
