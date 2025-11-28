<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Operations;

use CarlLee\NewebPay\Exceptions\NewebPayException;
use CarlLee\NewebPay\Operations\CreditPayment;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * 信用卡支付測試。
 */
class CreditPaymentTest extends TestCase
{
    private CreditPayment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->payment = new CreditPayment(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );
    }

    public function testSetMerchantOrderNo(): void
    {
        $this->payment->setMerchantOrderNo('TEST123456');

        $content = $this->payment->getRawContent();

        $this->assertEquals('TEST123456', $content['MerchantOrderNo']);
    }

    public function testSetMerchantOrderNoTooLong(): void
    {
        $this->expectException(NewebPayException::class);

        // 超過 30 字元
        $this->payment->setMerchantOrderNo(str_repeat('A', 31));
    }

    public function testSetAmt(): void
    {
        $this->payment->setAmt(1000);

        $content = $this->payment->getRawContent();

        $this->assertEquals(1000, $content['Amt']);
    }

    public function testSetAmtZero(): void
    {
        $this->expectException(NewebPayException::class);

        $this->payment->setAmt(0);
    }

    public function testSetItemDesc(): void
    {
        $this->payment->setItemDesc('測試商品');

        $content = $this->payment->getRawContent();

        $this->assertEquals('測試商品', $content['ItemDesc']);
    }

    public function testSetReturnURL(): void
    {
        $this->payment->setReturnURL('https://example.com/return');

        $content = $this->payment->getRawContent();

        $this->assertEquals('https://example.com/return', $content['ReturnURL']);
    }

    public function testSetNotifyURL(): void
    {
        $this->payment->setNotifyURL('https://example.com/notify');

        $content = $this->payment->getRawContent();

        $this->assertEquals('https://example.com/notify', $content['NotifyURL']);
    }

    public function testSetRedeem(): void
    {
        $this->payment->setRedeem(1);

        $content = $this->payment->getRawContent();

        $this->assertEquals(1, $content['CreditRed']);
    }

    public function testGetContent(): void
    {
        $this->payment
            ->setMerchantOrderNo('TEST123456')
            ->setAmt(1000)
            ->setItemDesc('測試商品')
            ->setReturnURL('https://example.com/return');

        $content = $this->payment->getContent();

        $this->assertArrayHasKey('MerchantID', $content);
        $this->assertArrayHasKey('TradeInfo', $content);
        $this->assertArrayHasKey('TradeSha', $content);
        $this->assertArrayHasKey('Version', $content);

        $this->assertEquals(self::TEST_MERCHANT_ID, $content['MerchantID']);
        $this->assertNotEmpty($content['TradeInfo']);
        $this->assertNotEmpty($content['TradeSha']);
    }

    public function testGetApiUrl(): void
    {
        $this->payment->setTestMode(true);
        $this->assertEquals('https://ccore.newebpay.com/MPG/mpg_gateway', $this->payment->getApiUrl());

        $this->payment->setTestMode(false);
        $this->assertEquals('https://core.newebpay.com/MPG/mpg_gateway', $this->payment->getApiUrl());
    }

    public function testValidationRequiresMerchantOrderNo(): void
    {
        $this->expectException(NewebPayException::class);

        $this->payment
            ->setAmt(1000)
            ->setItemDesc('測試商品')
            ->setReturnURL('https://example.com/return')
            ->getContent();
    }

    public function testCreditPaymentHasCreditEnabled(): void
    {
        $content = $this->payment->getRawContent();

        $this->assertEquals(1, $content['CREDIT']);
    }
}
