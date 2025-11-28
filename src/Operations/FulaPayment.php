<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Operations;

use CarlLee\NewebPay\Content;
use CarlLee\NewebPay\Parameter\PaymentType;

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
    protected function initContent(): void
    {
        parent::initContent();

        $this->content['FULA'] = 1;
    }

    /**
     * @inheritDoc
     */
    public function getPaymentMethod(): string
    {
        return PaymentType::FULA;
    }

    /**
     * @inheritDoc
     */
    protected function validation(): void
    {
        $this->validatorBaseParam();
    }
}
