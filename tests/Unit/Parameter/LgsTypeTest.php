<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Parameter;

use CarlLee\NewebPay\Parameter\LgsType;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * 物流類型測試。
 */
class LgsTypeTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertEquals('FAMILY', LgsType::Family->value);
        $this->assertEquals('UNIMART', LgsType::Seven->value);
        $this->assertEquals('HILIFE', LgsType::HiLife->value);
        $this->assertEquals('OKMART', LgsType::OkMart->value);
    }

    public function testValues(): void
    {
        $values = LgsType::values();

        $this->assertIsArray($values);
        $this->assertCount(4, $values);
        $this->assertContains('FAMILY', $values);
        $this->assertContains('UNIMART', $values);
        $this->assertContains('HILIFE', $values);
        $this->assertContains('OKMART', $values);
    }

    public function testName(): void
    {
        $this->assertEquals('全家', LgsType::Family->name());
        $this->assertEquals('7-ELEVEN', LgsType::Seven->name());
        $this->assertEquals('萊爾富', LgsType::HiLife->name());
        $this->assertEquals('OK mart', LgsType::OkMart->name());
    }

    public function testFromString(): void
    {
        $this->assertEquals(LgsType::Family, LgsType::fromString('FAMILY'));
        $this->assertEquals(LgsType::Seven, LgsType::fromString('UNIMART'));
        $this->assertNull(LgsType::fromString('INVALID'));
    }

    public function testTryFrom(): void
    {
        $this->assertEquals(LgsType::Family, LgsType::tryFrom('FAMILY'));
        $this->assertNull(LgsType::tryFrom('INVALID'));
    }
}
