<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Operations;

use CarlLee\NewebPay\Exceptions\NewebPayException;
use CarlLee\NewebPay\Operations\LinePayPayment;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * LINE Pay 支付測試。
 */
class LinePayPaymentTest extends TestCase
{
    private LinePayPayment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->payment = new LinePayPayment(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );
    }

    public function testLinePayPaymentHasLinePayEnabled(): void
    {
        $content = $this->payment->getRawContent();

        $this->assertEquals(1, $content['LINEPAY']);
    }

    public function testSetImageUrl(): void
    {
        $url = 'https://example.com/image.jpg';
        $this->payment->setImageUrl($url);

        $content = $this->payment->getRawContent();

        $this->assertEquals($url, $content['ImageUrl']);
    }

    public function testSetImageUrlTooLong(): void
    {
        $this->expectException(NewebPayException::class);

        // 超過 500 字元
        $url = 'https://example.com/' . str_repeat('a', 490);
        $this->payment->setImageUrl($url);
    }

    public function testGetContentWithValidData(): void
    {
        $this->payment
            ->setMerchantOrderNo('TEST123')
            ->setAmt(1000)
            ->setItemDesc('LINE Pay 測試')
            ->setReturnURL('https://example.com')
            ->setImageUrl('https://example.com/image.jpg');

        $content = $this->payment->getContent();

        $this->assertArrayHasKey('MerchantID', $content);
        $this->assertArrayHasKey('TradeInfo', $content);
        $this->assertArrayHasKey('TradeSha', $content);
    }

    public function testConstants(): void
    {
        $this->assertEquals(500, LinePayPayment::IMAGE_URL_MAX_LENGTH);
    }
}
