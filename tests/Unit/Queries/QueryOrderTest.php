<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Queries;

use CarlLee\NewebPay\Queries\QueryOrder;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * 交易查詢測試。
 */
class QueryOrderTest extends TestCase
{
    private QueryOrder $query;

    protected function setUp(): void
    {
        parent::setUp();

        $this->query = new QueryOrder(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );
    }

    public function testCreate(): void
    {
        $query = QueryOrder::create(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );

        $this->assertInstanceOf(QueryOrder::class, $query);
    }

    public function testSetTestMode(): void
    {
        $this->query->setTestMode(true);
        $this->assertEquals('https://ccore.newebpay.com', $this->query->getBaseUrl());

        $this->query->setTestMode(false);
        $this->assertEquals('https://core.newebpay.com', $this->query->getBaseUrl());
    }

    public function testGetApiUrl(): void
    {
        $this->query->setTestMode(true);
        $this->assertEquals(
            'https://ccore.newebpay.com/API/QueryTradeInfo',
            $this->query->getApiUrl()
        );

        $this->query->setTestMode(false);
        $this->assertEquals(
            'https://core.newebpay.com/API/QueryTradeInfo',
            $this->query->getApiUrl()
        );
    }

    public function testCheckValueGeneration(): void
    {
        // 使用反射測試 CheckValue 生成
        $reflection = new \ReflectionClass($this->query);
        $method = $reflection->getMethod('generateCheckValue');
        $method->setAccessible(true);

        $checkValue = $method->invoke($this->query, 'ORDER123', 1000);

        // CheckValue 應為 64 字元大寫十六進位
        $this->assertEquals(64, strlen($checkValue));
        $this->assertMatchesRegularExpression('/^[A-F0-9]+$/', $checkValue);
    }
}
