<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Operations;

use CarlLee\NewebPay\Content;
use Override;

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
    #[Override]
    protected function initContent(): void
    {
        parent::initContent();

        // 啟用 WebATM
        $this->content[\CarlLee\NewebPay\Parameter\PaymentType::WebAtm->value] = 1;
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
