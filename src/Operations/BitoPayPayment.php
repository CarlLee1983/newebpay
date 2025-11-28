<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Operations;

use CarlLee\NewebPay\Content;
use Override;

/**
 * BitoPay 支付。
 *
 * 支援 BitoPay 加密貨幣支付。
 */
class BitoPayPayment extends Content
{
    /**
     * @inheritDoc
     */
    #[Override]
    protected function initContent(): void
    {
        parent::initContent();

        // 啟用 BitoPay
        $this->content['BITOPAY'] = 1;
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
