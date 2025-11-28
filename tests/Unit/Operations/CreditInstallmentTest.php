<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Operations;

use CarlLee\NewebPay\Exceptions\NewebPayException;
use CarlLee\NewebPay\Operations\CreditInstallment;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * 信用卡分期付款測試。
 */
class CreditInstallmentTest extends TestCase
{
    private CreditInstallment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->payment = new CreditInstallment(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );
    }

    public function testCreditInstallmentHasCreditEnabled(): void
    {
        $content = $this->payment->getRawContent();

        $this->assertEquals(1, $content['CREDIT']);
    }

    public function testSetInstallmentSingle(): void
    {
        $this->payment->setInstallment(6);

        $content = $this->payment->getRawContent();

        $this->assertEquals('6', $content['InstFlag']);
    }

    public function testSetInstallmentMultiple(): void
    {
        $this->payment->setInstallment([3, 6, 12]);

        $content = $this->payment->getRawContent();

        $this->assertEquals('3,6,12', $content['InstFlag']);
    }

    public function testSetInstallmentInvalid(): void
    {
        $this->expectException(NewebPayException::class);

        $this->payment->setInstallment(5); // 5 期無效
    }

    public function testSetInstallmentInvalidInArray(): void
    {
        $this->expectException(NewebPayException::class);

        $this->payment->setInstallment([3, 5, 12]); // 5 期無效
    }

    public function testValidInstallments(): void
    {
        $validInstallments = [3, 6, 12, 18, 24, 30];

        foreach ($validInstallments as $inst) {
            $payment = new CreditInstallment(
                self::TEST_MERCHANT_ID,
                self::TEST_HASH_KEY,
                self::TEST_HASH_IV
            );
            $payment->setInstallment($inst);

            $content = $payment->getRawContent();
            $this->assertEquals((string) $inst, $content['InstFlag']);
        }
    }

    public function testValidationRequiresInstFlag(): void
    {
        $this->expectException(NewebPayException::class);

        $this->payment
            ->setMerchantOrderNo('TEST123')
            ->setAmt(1000)
            ->setItemDesc('測試')
            ->setReturnURL('https://example.com')
            ->getContent();
    }

    public function testGetContentWithValidData(): void
    {
        $this->payment
            ->setMerchantOrderNo('TEST123')
            ->setAmt(3000)
            ->setItemDesc('分期測試')
            ->setReturnURL('https://example.com')
            ->setInstallment([3, 6, 12]);

        $content = $this->payment->getContent();

        $this->assertArrayHasKey('MerchantID', $content);
        $this->assertArrayHasKey('TradeInfo', $content);
        $this->assertArrayHasKey('TradeSha', $content);
    }
}
