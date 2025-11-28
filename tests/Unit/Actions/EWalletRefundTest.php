<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Actions;

use CarlLee\NewebPay\Actions\EWalletRefund;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * 電子錢包退款測試。
 */
class EWalletRefundTest extends TestCase
{
    private EWalletRefund $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new EWalletRefund(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );
    }

    public function testCreate(): void
    {
        $action = EWalletRefund::create(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );

        $this->assertInstanceOf(EWalletRefund::class, $action);
    }

    public function testSetTestMode(): void
    {
        $this->action->setTestMode(true);
        $this->assertEquals('https://ccore.newebpay.com', $this->action->getBaseUrl());

        $this->action->setTestMode(false);
        $this->assertEquals('https://core.newebpay.com', $this->action->getBaseUrl());
    }

    public function testGetApiUrl(): void
    {
        $this->action->setTestMode(true);
        $this->assertEquals(
            'https://ccore.newebpay.com/API/EWallet/Refund',
            $this->action->getApiUrl()
        );
    }

    public function testBuildPayload(): void
    {
        // 使用反射測試 buildPayload
        $reflection = new \ReflectionClass($this->action);
        $method = $reflection->getMethod('buildPayload');
        $method->setAccessible(true);

        $payload = $method->invoke($this->action, [
            'MerchantID' => self::TEST_MERCHANT_ID,
            'MerchantOrderNo' => 'ORDER123',
            'Amount' => 500,
            'PaymentType' => 'LINEPAY',
            'TimeStamp' => '1704067200',
        ]);

        $this->assertArrayHasKey('MerchantID_', $payload);
        $this->assertArrayHasKey('PostData_', $payload);
        $this->assertArrayHasKey('Pos_', $payload);
        $this->assertEquals(self::TEST_MERCHANT_ID, $payload['MerchantID_']);
    }
}
