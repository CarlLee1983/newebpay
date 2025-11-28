<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Operations;

use CarlLee\NewebPay\Exceptions\NewebPayException;
use CarlLee\NewebPay\Operations\BarcodePayment;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * 超商條碼繳費支付測試。
 */
class BarcodePaymentTest extends TestCase
{
    private BarcodePayment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->payment = new BarcodePayment(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );
    }

    public function testBarcodePaymentHasBarcodeEnabled(): void
    {
        $content = $this->payment->getRawContent();

        $this->assertEquals(1, $content['BARCODE']);
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

        $this->payment->setAmt(19); // 低於 20 元
    }

    public function testSetAmtAboveMaximum(): void
    {
        $this->expectException(NewebPayException::class);

        $this->payment->setAmt(40001); // 超過 40000 元
    }

    public function testSetAmtAtMinimum(): void
    {
        $this->payment->setAmt(BarcodePayment::MIN_AMOUNT);

        $content = $this->payment->getRawContent();

        $this->assertEquals(20, $content['Amt']);
    }

    public function testSetAmtAtMaximum(): void
    {
        $this->payment->setAmt(BarcodePayment::MAX_AMOUNT);

        $content = $this->payment->getRawContent();

        $this->assertEquals(40000, $content['Amt']);
    }

    public function testConstants(): void
    {
        $this->assertEquals(20, BarcodePayment::MIN_AMOUNT);
        $this->assertEquals(40000, BarcodePayment::MAX_AMOUNT);
    }
}
