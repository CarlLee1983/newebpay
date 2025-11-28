<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Parameter;

use CarlLee\NewebPay\Parameter\TradeStatus;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * 交易狀態測試。
 */
class TradeStatusTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertEquals(1, TradeStatus::Success->value);
        $this->assertEquals(0, TradeStatus::Failed->value);
        $this->assertEquals(2, TradeStatus::Pending->value);
        $this->assertEquals(3, TradeStatus::Cancelled->value);
        $this->assertEquals(6, TradeStatus::Processing->value);
    }

    public function testIsSuccess(): void
    {
        $this->assertTrue(TradeStatus::Success->isSuccess());
        $this->assertFalse(TradeStatus::Failed->isSuccess());
        $this->assertFalse(TradeStatus::Pending->isSuccess());
    }

    public function testIsPending(): void
    {
        $this->assertTrue(TradeStatus::Pending->isPending());
        $this->assertFalse(TradeStatus::Success->isPending());
    }

    public function testIsFailed(): void
    {
        $this->assertTrue(TradeStatus::Failed->isFailed());
        $this->assertFalse(TradeStatus::Success->isFailed());
    }

    public function testDescription(): void
    {
        $this->assertEquals('交易成功', TradeStatus::Success->description());
        $this->assertEquals('交易付款失敗', TradeStatus::Failed->description());
        $this->assertEquals('交易等待付款中', TradeStatus::Pending->description());
        $this->assertEquals('交易已取消', TradeStatus::Cancelled->description());
        $this->assertEquals('交易處理中', TradeStatus::Processing->description());
    }

    public function testFromValue(): void
    {
        $this->assertEquals(TradeStatus::Success, TradeStatus::fromValue(1));
        $this->assertEquals(TradeStatus::Success, TradeStatus::fromValue('1'));
        $this->assertEquals(TradeStatus::Failed, TradeStatus::fromValue(0));
        $this->assertNull(TradeStatus::fromValue(99));
    }

    public function testTryFrom(): void
    {
        $this->assertEquals(TradeStatus::Success, TradeStatus::tryFrom(1));
        $this->assertNull(TradeStatus::tryFrom(99));
    }

    public function testValues(): void
    {
        $values = TradeStatus::values();

        $this->assertIsArray($values);
        $this->assertContains(1, $values);
        $this->assertContains(0, $values);
        $this->assertContains(2, $values);
    }
}
