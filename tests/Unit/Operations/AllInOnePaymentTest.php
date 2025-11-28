<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Operations;

use CarlLee\NewebPay\Exceptions\NewebPayException;
use CarlLee\NewebPay\Operations\AllInOnePayment;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * 全支付方式測試。
 */
class AllInOnePaymentTest extends TestCase
{
    private AllInOnePayment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->payment = new AllInOnePayment(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );
    }

    public function testEnableCredit(): void
    {
        $this->payment->enableCredit();

        $content = $this->payment->getRawContent();

        $this->assertEquals(1, $content['CREDIT']);
    }

    public function testEnableWebAtm(): void
    {
        $this->payment->enableWebAtm();

        $content = $this->payment->getRawContent();

        $this->assertEquals(1, $content['WEBATM']);
    }

    public function testEnableAtm(): void
    {
        $this->payment->enableAtm();

        $content = $this->payment->getRawContent();

        $this->assertEquals(1, $content['VACC']);
    }

    public function testEnableCvs(): void
    {
        $this->payment->enableCvs();

        $content = $this->payment->getRawContent();

        $this->assertEquals(1, $content['CVS']);
    }

    public function testEnableBarcode(): void
    {
        $this->payment->enableBarcode();

        $content = $this->payment->getRawContent();

        $this->assertEquals(1, $content['BARCODE']);
    }

    public function testEnableLinePay(): void
    {
        $this->payment->enableLinePay();

        $content = $this->payment->getRawContent();

        $this->assertEquals(1, $content['LINEPAY']);
    }

    public function testEnableTaiwanPay(): void
    {
        $this->payment->enableTaiwanPay();

        $content = $this->payment->getRawContent();

        $this->assertEquals(1, $content['TAIWANPAY']);
    }

    public function testEnableEsunWallet(): void
    {
        $this->payment->enableEsunWallet();

        $content = $this->payment->getRawContent();

        $this->assertEquals(1, $content['ESUNWALLET']);
    }

    public function testEnableBitoPay(): void
    {
        $this->payment->enableBitoPay();

        $content = $this->payment->getRawContent();

        $this->assertEquals(1, $content['BITOPAY']);
    }

    public function testEnableCvscom(): void
    {
        $this->payment->enableCvscom();

        $content = $this->payment->getRawContent();

        $this->assertEquals(1, $content['CVSCOM']);
    }

    public function testEnableInstallment(): void
    {
        $this->payment->enableInstallment([3, 6, 12]);

        $content = $this->payment->getRawContent();

        $this->assertEquals(1, $content['CREDIT']);
        $this->assertEquals('3,6,12', $content['InstFlag']);
    }

    public function testEnableInstallmentWithString(): void
    {
        $this->payment->enableInstallment('3,6,12');

        $content = $this->payment->getRawContent();

        $this->assertEquals('3,6,12', $content['InstFlag']);
    }

    public function testEnableAll(): void
    {
        $this->payment->enableAll();

        $content = $this->payment->getRawContent();

        $this->assertEquals(1, $content['CREDIT']);
        $this->assertEquals(1, $content['WEBATM']);
        $this->assertEquals(1, $content['VACC']);
        $this->assertEquals(1, $content['CVS']);
        $this->assertEquals(1, $content['BARCODE']);
        $this->assertEquals(1, $content['LINEPAY']);
        $this->assertEquals(1, $content['TAIWANPAY']);
        $this->assertEquals(1, $content['ESUNWALLET']);
        $this->assertEquals(1, $content['BITOPAY']);
    }

    public function testValidationRequiresAtLeastOnePaymentMethod(): void
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
            ->setAmt(1000)
            ->setItemDesc('測試')
            ->setReturnURL('https://example.com')
            ->enableCredit()
            ->enableAtm();

        $content = $this->payment->getContent();

        $this->assertArrayHasKey('MerchantID', $content);
        $this->assertArrayHasKey('TradeInfo', $content);
        $this->assertArrayHasKey('TradeSha', $content);
    }

    public function testDisablePaymentMethod(): void
    {
        $this->payment
            ->enableCredit()
            ->enableCredit(0); // 停用

        $content = $this->payment->getRawContent();

        $this->assertEquals(0, $content['CREDIT']);
    }
}
