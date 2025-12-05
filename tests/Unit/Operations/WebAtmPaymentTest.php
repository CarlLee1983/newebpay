<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Operations;

use CarlLee\NewebPay\Operations\WebAtmPayment;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * WebATM 支付測試。
 */
class WebAtmPaymentTest extends TestCase
{
    private WebAtmPayment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->payment = new WebAtmPayment(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );
    }

    public function testWebAtmPaymentHasWebAtmEnabled(): void
    {
        $content = $this->payment->getRawContent();

        $this->assertEquals(1, $content['WEBATM']);
    }
}
