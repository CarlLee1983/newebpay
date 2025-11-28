<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Actions;

use CarlLee\NewebPay\Actions\CreditClose;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * 信用卡請退款測試。
 */
class CreditCloseTest extends TestCase
{
    private CreditClose $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new CreditClose(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );
    }

    public function testCreate(): void
    {
        $action = CreditClose::create(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );

        $this->assertInstanceOf(CreditClose::class, $action);
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
            'https://ccore.newebpay.com/API/CreditCard/Close',
            $this->action->getApiUrl()
        );
    }

    public function testConstants(): void
    {
        $this->assertEquals(1, CreditClose::CLOSE_TYPE_PAY);
        $this->assertEquals(2, CreditClose::CLOSE_TYPE_REFUND);
    }

    public function testBuildPayload(): void
    {
        // 使用反射測試 buildPayload
        $reflection = new \ReflectionClass($this->action);
        $method = $reflection->getMethod('buildPayload');
        $method->setAccessible(true);

        $payload = $method->invoke($this->action, [
            'RespondType' => 'JSON',
            'Version' => '1.1',
            'Amt' => 500,
            'MerchantOrderNo' => 'ORDER123',
            'IndexType' => '2',
            'TimeStamp' => '1704067200',
            'CloseType' => 2,
        ]);

        $this->assertArrayHasKey('MerchantID_', $payload);
        $this->assertArrayHasKey('PostData_', $payload);
        $this->assertEquals(self::TEST_MERCHANT_ID, $payload['MerchantID_']);
        $this->assertNotEmpty($payload['PostData_']);
    }
}
