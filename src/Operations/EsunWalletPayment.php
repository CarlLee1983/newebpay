<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Operations;

use CarlLee\NewebPay\Content;

/**
 * 玉山 Wallet 支付。
 *
 * 支援玉山 Wallet 電子錢包付款。
 */
class EsunWalletPayment extends Content
{
    /**
     * @inheritDoc
     */
    protected function initContent(): void
    {
        parent::initContent();

        // 啟用玉山 Wallet
        $this->content['ESUNWALLET'] = 1;
    }

    /**
     * @inheritDoc
     */
    protected function validation(): void
    {
        $this->validateBaseParams();
    }
}
