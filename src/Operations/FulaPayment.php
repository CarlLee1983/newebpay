<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Operations;

use CarlLee\NewebPay\Content;
use CarlLee\NewebPay\Parameter\PaymentType;
use Override;

/**
 * Fula 付啦付款。
 *
 * BNPL 先買後付服務。
 */
class FulaPayment extends Content
{
    /**
     * @inheritDoc
     */
    #[Override]
    protected function initContent(): void
    {
        parent::initContent();

        $this->content[PaymentType::Fula->value] = 1;
    }

    /**
     * 取得支付方式。
     */
    public function getPaymentMethod(): string
    {
        return PaymentType::Fula->value;
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
