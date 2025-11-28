<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Actions;

use CarlLee\NewebPay\Actions\CreditCancel;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * 信用卡取消授權測試。
 */
class CreditCancelTest extends TestCase
{
    private CreditCancel $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new CreditCancel(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );
    }

    public function testCreate(): void
    {
        $action = CreditCancel::create(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );

        $this->assertInstanceOf(CreditCancel::class, $action);
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
            'https://ccore.newebpay.com/API/CreditCard/Cancel',
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
            'RespondType' => 'JSON',
            'Version' => '1.0',
            'Amt' => 1000,
            'MerchantOrderNo' => 'ORDER123',
            'IndexType' => '2',
            'TimeStamp' => '1704067200',
        ]);

        $this->assertArrayHasKey('MerchantID_', $payload);
        $this->assertArrayHasKey('PostData_', $payload);
        $this->assertEquals(self::TEST_MERCHANT_ID, $payload['MerchantID_']);
        $this->assertNotEmpty($payload['PostData_']);
    }
}
