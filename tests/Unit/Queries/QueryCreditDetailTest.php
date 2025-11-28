<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Queries;

use CarlLee\NewebPay\Queries\QueryCreditDetail;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * 信用卡交易明細查詢測試。
 */
class QueryCreditDetailTest extends TestCase
{
    private QueryCreditDetail $query;

    protected function setUp(): void
    {
        parent::setUp();

        $this->query = new QueryCreditDetail(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );
    }

    public function testCreate(): void
    {
        $query = QueryCreditDetail::create(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );

        $this->assertInstanceOf(QueryCreditDetail::class, $query);
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
            'https://ccore.newebpay.com/API/CreditCard/TradeDetail',
            $this->query->getApiUrl()
        );
    }
}
