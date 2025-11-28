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
    public function testConstants(): void
    {
        $this->assertEquals(1, TradeStatus::SUCCESS);
        $this->assertEquals(0, TradeStatus::FAILED);
        $this->assertEquals(2, TradeStatus::PENDING);
        $this->assertEquals(3, TradeStatus::CANCELLED);
        $this->assertEquals(6, TradeStatus::PROCESSING);
    }

    public function testIsSuccess(): void
    {
        $this->assertTrue(TradeStatus::isSuccess(1));
        $this->assertTrue(TradeStatus::isSuccess('1'));
        $this->assertFalse(TradeStatus::isSuccess(0));
        $this->assertFalse(TradeStatus::isSuccess(2));
    }

    public function testIsPending(): void
    {
        $this->assertTrue(TradeStatus::isPending(2));
        $this->assertTrue(TradeStatus::isPending('2'));
        $this->assertFalse(TradeStatus::isPending(1));
    }

    public function testIsFailed(): void
    {
        $this->assertTrue(TradeStatus::isFailed(0));
        $this->assertTrue(TradeStatus::isFailed('0'));
        $this->assertFalse(TradeStatus::isFailed(1));
    }

    public function testGetDescription(): void
    {
        $this->assertEquals('交易成功', TradeStatus::getDescription(1));
        $this->assertEquals('交易付款失敗', TradeStatus::getDescription(0));
        $this->assertEquals('交易等待付款中', TradeStatus::getDescription(2));
        $this->assertEquals('交易已取消', TradeStatus::getDescription(3));
        $this->assertEquals('交易處理中', TradeStatus::getDescription(6));
        $this->assertEquals('未知狀態', TradeStatus::getDescription(99));
    }
}
