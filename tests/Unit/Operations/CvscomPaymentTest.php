<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Operations;

use CarlLee\NewebPay\Exceptions\NewebPayException;
use CarlLee\NewebPay\Operations\CvscomPayment;
use CarlLee\NewebPay\Parameter\LgsType;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * 超商取貨付款支付測試。
 */
class CvscomPaymentTest extends TestCase
{
    private CvscomPayment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->payment = new CvscomPayment(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );
    }

    public function testCvscomPaymentHasCvscomEnabled(): void
    {
        $content = $this->payment->getRawContent();

        $this->assertEquals(1, $content['CVSCOM']);
    }

    public function testSetLgsType(): void
    {
        $this->payment->setLgsType(LgsType::Family);

        $content = $this->payment->getRawContent();

        $this->assertEquals('FAMILY', $content['LgsType']);
    }

    public function testSetLgsTypeAll(): void
    {
        $types = [LgsType::Family, LgsType::Seven, LgsType::HiLife, LgsType::OkMart];

        foreach ($types as $type) {
            $payment = new CvscomPayment(
                self::TEST_MERCHANT_ID,
                self::TEST_HASH_KEY,
                self::TEST_HASH_IV
            );
            $payment->setLgsType($type);

            $content = $payment->getRawContent();
            $this->assertEquals($type->value, $content['LgsType']);
        }
    }

    public function testSetLgsTypeInvalid(): void
    {
        $this->expectException(NewebPayException::class);

        $this->payment->setLgsType('INVALID');
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

        $this->payment->setAmt(29);
    }

    public function testSetAmtAboveMaximum(): void
    {
        $this->expectException(NewebPayException::class);

        $this->payment->setAmt(20001);
    }

    public function testConstants(): void
    {
        $this->assertEquals(30, CvscomPayment::MIN_AMOUNT);
        $this->assertEquals(20000, CvscomPayment::MAX_AMOUNT);
    }
}
