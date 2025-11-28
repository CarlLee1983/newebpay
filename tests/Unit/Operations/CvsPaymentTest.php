<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Operations;

use CarlLee\NewebPay\Exceptions\NewebPayException;
use CarlLee\NewebPay\Operations\CvsPayment;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * 超商代碼繳費支付測試。
 */
class CvsPaymentTest extends TestCase
{
    private CvsPayment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->payment = new CvsPayment(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );
    }

    public function testCvsPaymentHasCvsEnabled(): void
    {
        $content = $this->payment->getRawContent();

        $this->assertEquals(1, $content['CVS']);
    }

    public function testSetAmtWithinRange(): void
    {
        $this->payment->setAmt(100);

        $content = $this->payment->getRawContent();

        $this->assertEquals(100, $content['Amt']);
    }

    public function testSetAmtBelowMinimum(): void
    {
        $this->expectException(NewebPayException::class);

        $this->payment->setAmt(29); // 低於 30 元
    }

    public function testSetAmtAboveMaximum(): void
    {
        $this->expectException(NewebPayException::class);

        $this->payment->setAmt(20001); // 超過 20000 元
    }

    public function testSetAmtAtMinimum(): void
    {
        $this->payment->setAmt(CvsPayment::MIN_AMOUNT);

        $content = $this->payment->getRawContent();

        $this->assertEquals(30, $content['Amt']);
    }

    public function testSetAmtAtMaximum(): void
    {
        $this->payment->setAmt(CvsPayment::MAX_AMOUNT);

        $content = $this->payment->getRawContent();

        $this->assertEquals(20000, $content['Amt']);
    }
}
