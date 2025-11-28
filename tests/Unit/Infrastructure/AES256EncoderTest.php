<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Infrastructure;

use CarlLee\NewebPay\Exceptions\NewebPayException;
use CarlLee\NewebPay\Infrastructure\AES256Encoder;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * AES256 編碼器測試。
 */
class AES256EncoderTest extends TestCase
{
    private AES256Encoder $encoder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->encoder = new AES256Encoder(
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );
    }

    public function testEncrypt(): void
    {
        $data = [
            'MerchantID' => self::TEST_MERCHANT_ID,
            'Amt' => 100,
            'ItemDesc' => '測試商品',
        ];

        $encrypted = $this->encoder->encrypt($data);

        $this->assertIsString($encrypted);
        $this->assertNotEmpty($encrypted);
        // 加密後應為十六進位字串
        $this->assertMatchesRegularExpression('/^[a-f0-9]+$/i', $encrypted);
    }

    public function testDecrypt(): void
    {
        $originalData = [
            'MerchantID' => self::TEST_MERCHANT_ID,
            'Amt' => 100,
            'ItemDesc' => '測試商品',
        ];

        // 先加密
        $encrypted = $this->encoder->encrypt($originalData);

        // 再解密
        $decrypted = $this->encoder->decrypt($encrypted);

        $this->assertEquals($originalData['MerchantID'], $decrypted['MerchantID']);
        $this->assertEquals($originalData['Amt'], $decrypted['Amt']);
        $this->assertEquals($originalData['ItemDesc'], $decrypted['ItemDesc']);
    }

    public function testDecryptInvalidData(): void
    {
        $this->expectException(NewebPayException::class);

        $this->encoder->decrypt('invalid_data');
    }

    public function testCreate(): void
    {
        $encoder = AES256Encoder::create(self::TEST_HASH_KEY, self::TEST_HASH_IV);

        $this->assertInstanceOf(AES256Encoder::class, $encoder);
    }
}
