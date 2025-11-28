<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Operations;

use CarlLee\NewebPay\Content;

/**
 * WebATM 網路 ATM 支付。
 *
 * 消費者透過網路銀行進行即時轉帳付款。
 */
class WebAtmPayment extends Content
{
    /**
     * @inheritDoc
     */
    protected function initContent(): void
    {
        parent::initContent();

        // 啟用 WebATM
        $this->content['WEBATM'] = 1;
    }

    /**
     * @inheritDoc
     */
    protected function validation(): void
    {
        $this->validateBaseParams();
    }
}
