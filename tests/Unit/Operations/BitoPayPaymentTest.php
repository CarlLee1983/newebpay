<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Operations;

use CarlLee\NewebPay\Operations\BitoPayPayment;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * BitoPay 支付測試。
 */
class BitoPayPaymentTest extends TestCase
{
    private BitoPayPayment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->payment = new BitoPayPayment(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );
    }

    public function testBitoPayPaymentHasBitoPayEnabled(): void
    {
        $content = $this->payment->getRawContent();

        $this->assertEquals(1, $content['BITOPAY']);
    }
}
