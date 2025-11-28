<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Operations;

use CarlLee\NewebPay\Exceptions\NewebPayException;
use CarlLee\NewebPay\Operations\AtmPayment;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * ATM 轉帳支付測試。
 */
class AtmPaymentTest extends TestCase
{
    private AtmPayment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->payment = new AtmPayment(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );
    }

    public function testAtmPaymentHasVaccEnabled(): void
    {
        $content = $this->payment->getRawContent();

        $this->assertEquals(1, $content['VACC']);
    }

    public function testSetBankType(): void
    {
        $this->payment->setBankType(AtmPayment::BANK_BOT);

        $content = $this->payment->getRawContent();

        $this->assertEquals('BOT', $content['BankType']);
    }

    public function testSetBankTypeInvalid(): void
    {
        $this->expectException(NewebPayException::class);

        $this->payment->setBankType('INVALID');
    }

    public function testSetExpireDate(): void
    {
        $this->payment->setExpireDate('2025-12-31');

        $content = $this->payment->getRawContent();

        $this->assertEquals('2025-12-31', $content['ExpireDate']);
    }
}
