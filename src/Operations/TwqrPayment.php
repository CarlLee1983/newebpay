<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Operations;

use CarlLee\NewebPay\Content;
use CarlLee\NewebPay\Parameter\PaymentType;
use Override;

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
    #[Override]
    protected function initContent(): void
    {
        parent::initContent();

        $this->content['TWQR'] = 1;
    }

    /**
     * 取得支付方式。
     */
    public function getPaymentMethod(): string
    {
        return PaymentType::Twqr->value;
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
