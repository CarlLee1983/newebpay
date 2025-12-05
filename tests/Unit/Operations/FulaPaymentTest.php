<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Operations;

use CarlLee\NewebPay\Operations\FulaPayment;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * Fula 支付測試。
 */
class FulaPaymentTest extends TestCase
{
    private FulaPayment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->payment = new FulaPayment(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );
    }

    public function testFulaPaymentHasFulaEnabled(): void
    {
        $content = $this->payment->getRawContent();

        $this->assertEquals(1, $content['FULA']);
    }
}
