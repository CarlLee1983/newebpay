<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Operations;

use CarlLee\NewebPay\Operations\TwqrPayment;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * TWQR 支付測試。
 */
class TwqrPaymentTest extends TestCase
{
    private TwqrPayment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->payment = new TwqrPayment(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );
    }

    public function testTwqrPaymentHasTwqrEnabled(): void
    {
        $content = $this->payment->getRawContent();

        $this->assertEquals(1, $content['TWQR']);
    }
}
