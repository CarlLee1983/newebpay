<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Operations;

use CarlLee\NewebPay\Content;
use CarlLee\NewebPay\Parameter\PaymentType;

/**
 * TWQR 付款。
 *
 * 台灣通用 QR Code 支付。
 */
class TwqrPayment extends Content
{
    /**
     * @inheritDoc
     */
    protected function initContent(): void
    {
        parent::initContent();

        $this->content['TWQR'] = 1;
    }

    /**
     * @inheritDoc
     */
    public function getPaymentMethod(): string
    {
        return PaymentType::TWQR;
    }

    /**
     * @inheritDoc
     */
    protected function validation(): void
    {
        $this->validatorBaseParam();
    }
}
