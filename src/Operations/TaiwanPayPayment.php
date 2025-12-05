<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Operations;

use CarlLee\NewebPay\Content;
use Override;

/**
 * 台灣 Pay 支付。
 *
 * 支援台灣 Pay 行動支付。
 */
class TaiwanPayPayment extends Content
{
    /**
     * @inheritDoc
     */
    #[Override]
    protected function initContent(): void
    {
        parent::initContent();

        // 啟用台灣 Pay
        $this->content[\CarlLee\NewebPay\Parameter\PaymentType::TaiwanPay->value] = 1;
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
