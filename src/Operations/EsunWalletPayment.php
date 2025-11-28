<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Operations;

use CarlLee\NewebPay\Content;
use Override;

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
    #[Override]
    protected function initContent(): void
    {
        parent::initContent();

        // 啟用玉山 Wallet
        $this->content['ESUNWALLET'] = 1;
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
