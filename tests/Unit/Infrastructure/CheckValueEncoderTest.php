<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Infrastructure;

use CarlLee\NewebPay\Exceptions\NewebPayException;
use CarlLee\NewebPay\Infrastructure\CheckValueEncoder;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * CheckValue 編碼器測試。
 */
class CheckValueEncoderTest extends TestCase
{
    private CheckValueEncoder $encoder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->encoder = new CheckValueEncoder(
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );
    }

    public function testGenerate(): void
    {
        $tradeInfo = 'test_trade_info_data';

        $checkValue = $this->encoder->generate($tradeInfo);

        $this->assertIsString($checkValue);
        $this->assertNotEmpty($checkValue);
        // SHA256 應為 64 字元大寫十六進位
        $this->assertEquals(64, strlen($checkValue));
        $this->assertMatchesRegularExpression('/^[A-F0-9]+$/', $checkValue);
    }

    public function testVerify(): void
    {
        $tradeInfo = 'test_trade_info_data';

        // 產生 CheckValue
        $checkValue = $this->encoder->generate($tradeInfo);

        // 驗證應通過
        $this->assertTrue($this->encoder->verify($tradeInfo, $checkValue));
    }

    public function testVerifyWithInvalidCheckValue(): void
    {
        $tradeInfo = 'test_trade_info_data';

        // 使用錯誤的 CheckValue
        $this->assertFalse($this->encoder->verify($tradeInfo, 'invalid_check_value'));
    }

    public function testVerifyOrFail(): void
    {
        $tradeInfo = 'test_trade_info_data';
        $checkValue = $this->encoder->generate($tradeInfo);

        // 不應拋出例外
        $this->encoder->verifyOrFail($tradeInfo, $checkValue);

        $this->assertTrue(true); // 如果到達這裡表示沒有拋出例外
    }

    public function testVerifyOrFailWithInvalidCheckValue(): void
    {
        $this->expectException(NewebPayException::class);

        $this->encoder->verifyOrFail('test_data', 'invalid_check_value');
    }

    public function testCreate(): void
    {
        $encoder = CheckValueEncoder::create(self::TEST_HASH_KEY, self::TEST_HASH_IV);

        $this->assertInstanceOf(CheckValueEncoder::class, $encoder);
    }
}
