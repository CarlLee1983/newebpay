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
    public function testConstants(): void
    {
        $this->assertEquals('FAMILY', LgsType::FAMILY);
        $this->assertEquals('UNIMART', LgsType::SEVEN);
        $this->assertEquals('HILIFE', LgsType::HILIFE);
        $this->assertEquals('OKMART', LgsType::OKMART);
    }

    public function testAll(): void
    {
        $all = LgsType::all();

        $this->assertIsArray($all);
        $this->assertCount(4, $all);
        $this->assertContains('FAMILY', $all);
        $this->assertContains('UNIMART', $all);
        $this->assertContains('HILIFE', $all);
        $this->assertContains('OKMART', $all);
    }

    public function testGetName(): void
    {
        $this->assertEquals('全家', LgsType::getName('FAMILY'));
        $this->assertEquals('7-ELEVEN', LgsType::getName('UNIMART'));
        $this->assertEquals('萊爾富', LgsType::getName('HILIFE'));
        $this->assertEquals('OK mart', LgsType::getName('OKMART'));
        $this->assertEquals('未知', LgsType::getName('INVALID'));
    }
}
