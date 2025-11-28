<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit;

use CarlLee\NewebPay\Exceptions\NewebPayException;
use CarlLee\NewebPay\Operations\CreditPayment;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * Content 基類測試。
 *
 * 使用 CreditPayment 作為具體實作來測試 Content 基類功能。
 */
class ContentTest extends TestCase
{
    private CreditPayment $content;

    protected function setUp(): void
    {
        parent::setUp();

        $this->content = new CreditPayment(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );
    }

    public function testSetMerchantID(): void
    {
        $this->content->setMerchantID('NEW_MERCHANT');

        $this->assertEquals('NEW_MERCHANT', $this->content->getMerchantID());
    }

    public function testSetTimeStamp(): void
    {
        $this->content->setTimeStamp(1704067200);

        $content = $this->content->getRawContent();

        $this->assertEquals('1704067200', $content['TimeStamp']);
    }

    public function testSetTradeLimit(): void
    {
        $this->content->setTradeLimit(300);

        $content = $this->content->getRawContent();

        $this->assertEquals(300, $content['TradeLimit']);
    }

    public function testSetTradeLimitTooLow(): void
    {
        $this->expectException(NewebPayException::class);

        $this->content->setTradeLimit(59);
    }

    public function testSetTradeLimitTooHigh(): void
    {
        $this->expectException(NewebPayException::class);

        $this->content->setTradeLimit(901);
    }

    public function testSetExpireDate(): void
    {
        $this->content->setExpireDate('2024-12-31');

        $content = $this->content->getRawContent();

        $this->assertEquals('2024-12-31', $content['ExpireDate']);
    }

    public function testSetCustomerURL(): void
    {
        $url = 'https://example.com/customer';
        $this->content->setCustomerURL($url);

        $content = $this->content->getRawContent();

        $this->assertEquals($url, $content['CustomerURL']);
    }

    public function testSetClientBackURL(): void
    {
        $url = 'https://example.com/back';
        $this->content->setClientBackURL($url);

        $content = $this->content->getRawContent();

        $this->assertEquals($url, $content['ClientBackURL']);
    }

    public function testSetEmail(): void
    {
        $this->content->setEmail('test@example.com');

        $content = $this->content->getRawContent();

        $this->assertEquals('test@example.com', $content['Email']);
    }

    public function testSetEmailTooLong(): void
    {
        $this->expectException(NewebPayException::class);

        // 超過 50 字元
        $this->content->setEmail(str_repeat('a', 40) . '@example.com');
    }

    public function testSetEmailModify(): void
    {
        $this->content->setEmailModify(1);

        $content = $this->content->getRawContent();

        $this->assertEquals(1, $content['EmailModify']);
    }

    public function testSetOrderComment(): void
    {
        $this->content->setOrderComment('這是備註');

        $content = $this->content->getRawContent();

        $this->assertEquals('這是備註', $content['OrderComment']);
    }

    public function testSetLangType(): void
    {
        $this->content->setLangType('en');

        $content = $this->content->getRawContent();

        $this->assertEquals('en', $content['LangType']);
    }

    public function testSetTestMode(): void
    {
        $this->content->setTestMode(true);
        $this->assertTrue($this->content->isTestMode());

        $this->content->setTestMode(false);
        $this->assertFalse($this->content->isTestMode());
    }

    public function testGetBaseUrl(): void
    {
        $this->content->setTestMode(true);
        $this->assertEquals('https://ccore.newebpay.com', $this->content->getBaseUrl());

        $this->content->setTestMode(false);
        $this->assertEquals('https://core.newebpay.com', $this->content->getBaseUrl());
    }

    public function testGetRequestPath(): void
    {
        $this->assertEquals('/MPG/mpg_gateway', $this->content->getRequestPath());
    }

    public function testSet(): void
    {
        $this->content->set('CustomField', 'CustomValue');

        $content = $this->content->getRawContent();

        $this->assertEquals('CustomValue', $content['CustomField']);
    }

    public function testGet(): void
    {
        $this->content->set('CustomField', 'CustomValue');

        $this->assertEquals('CustomValue', $this->content->get('CustomField'));
        $this->assertNull($this->content->get('NonExistent'));
        $this->assertEquals('default', $this->content->get('NonExistent', 'default'));
    }

    public function testInitialContent(): void
    {
        $content = $this->content->getRawContent();

        $this->assertArrayHasKey('MerchantID', $content);
        $this->assertArrayHasKey('TimeStamp', $content);
        $this->assertArrayHasKey('Version', $content);
        $this->assertArrayHasKey('RespondType', $content);
        $this->assertArrayHasKey('LangType', $content);

        $this->assertEquals(self::TEST_MERCHANT_ID, $content['MerchantID']);
        $this->assertEquals('2.0', $content['Version']);
        $this->assertEquals('JSON', $content['RespondType']);
        $this->assertEquals('zh-tw', $content['LangType']);
    }

    public function testConstants(): void
    {
        $this->assertEquals(30, CreditPayment::MERCHANT_ORDER_NO_MAX_LENGTH);
        $this->assertEquals(50, CreditPayment::ITEM_DESC_MAX_LENGTH);
        $this->assertEquals(50, CreditPayment::EMAIL_MAX_LENGTH);
    }
}
