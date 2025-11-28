<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Operations;

use CarlLee\NewebPay\Content;

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
    protected function initContent(): void
    {
        parent::initContent();

        // 啟用台灣 Pay
        $this->content['TAIWANPAY'] = 1;
    }

    /**
     * @inheritDoc
     */
    protected function validation(): void
    {
        $this->validateBaseParams();
    }
}
