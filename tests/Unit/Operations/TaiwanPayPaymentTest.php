<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Operations;

use CarlLee\NewebPay\Operations\TaiwanPayPayment;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * TaiwanPay 支付測試。
 */
class TaiwanPayPaymentTest extends TestCase
{
    private TaiwanPayPayment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->payment = new TaiwanPayPayment(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );
    }

    public function testTaiwanPayPaymentHasTaiwanPayEnabled(): void
    {
        $content = $this->payment->getRawContent();

        $this->assertEquals(1, $content['TAIWANPAY']);
    }
}
