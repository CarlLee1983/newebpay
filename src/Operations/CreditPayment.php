<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Operations;

use CarlLee\NewebPay\Content;
use Override;

/**
 * 信用卡一次付清支付。
 *
 * 支援信用卡一次付清、紅利折抵等功能。
 */
class CreditPayment extends Content
{
    /**
     * @inheritDoc
     */
    #[Override]
    protected function initContent(): void
    {
        parent::initContent();

        // 啟用信用卡付款
        $this->content['CREDIT'] = 1;
    }

    /**
     * 設定是否啟用紅利折抵。
     *
     * @param int $enable 0=不啟用, 1=啟用
     * @return static
     */
    public function setRedeem(int $enable): static
    {
        $this->content['CreditRed'] = $enable;

        return $this;
    }

    /**
     * 設定是否啟用銀聯卡。
     *
     * @param int $enable 0=不啟用, 1=啟用
     * @return static
     */
    public function setUnionPay(int $enable): static
    {
        $this->content['UNIONPAY'] = $enable;

        return $this;
    }

    /**
     * 設定是否啟用 Google Pay。
     *
     * @param int $enable 0=不啟用, 1=啟用
     * @return static
     */
    public function setGooglePay(int $enable): static
    {
        $this->content['ANDROIDPAY'] = $enable;

        return $this;
    }

    /**
     * 設定是否啟用 Samsung Pay。
     *
     * @param int $enable 0=不啟用, 1=啟用
     * @return static
     */
    public function setSamsungPay(int $enable): static
    {
        $this->content['SAMSUNGPAY'] = $enable;

        return $this;
    }

    /**
     * 設定信用卡快速結帳。
     *
     * @param int $enable 0=不啟用, 1=啟用
     * @return static
     */
    public function setTokenTerm(int $enable): static
    {
        $this->content['TokenTerm'] = $enable;

        return $this;
    }

    /**
     * 設定信用卡快速結帳使用者識別碼。
     *
     * @param string $tokenTermId 識別碼
     * @return static
     */
    public function setTokenTermDemand(string $tokenTermId): static
    {
        $this->content['TokenTermDemand'] = $tokenTermId;

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
