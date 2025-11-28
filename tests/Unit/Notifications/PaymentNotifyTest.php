<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Notifications;

use CarlLee\NewebPay\Exceptions\NewebPayException;
use CarlLee\NewebPay\Infrastructure\AES256Encoder;
use CarlLee\NewebPay\Infrastructure\CheckValueEncoder;
use CarlLee\NewebPay\Notifications\PaymentNotify;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * 支付通知處理器測試。
 */
class PaymentNotifyTest extends TestCase
{
    private PaymentNotify $notify;
    private AES256Encoder $aesEncoder;
    private CheckValueEncoder $checkValueEncoder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->notify = new PaymentNotify(
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );

        $this->aesEncoder = new AES256Encoder(
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );

        $this->checkValueEncoder = new CheckValueEncoder(
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );
    }

    public function testVerifyWithValidData(): void
    {
        $mockData = [
            'Status' => 'SUCCESS',
            'Message' => '授權成功',
            'MerchantID' => self::TEST_MERCHANT_ID,
            'Result' => [
                'MerchantOrderNo' => 'TEST123456',
                'TradeNo' => '20231231000001',
                'Amt' => 1000,
                'PaymentType' => 'CREDIT',
                'PayTime' => '2023-12-31 12:00:00',
            ],
        ];

        $tradeInfo = $this->aesEncoder->encrypt($mockData);
        $tradeSha = $this->checkValueEncoder->generate($tradeInfo);

        $data = [
            'TradeInfo' => $tradeInfo,
            'TradeSha' => $tradeSha,
            'MerchantID' => self::TEST_MERCHANT_ID,
        ];

        $result = $this->notify->verify($data);

        $this->assertTrue($result);
        $this->assertTrue($this->notify->isVerified());
        $this->assertTrue($this->notify->isSuccess());
        $this->assertEquals('SUCCESS', $this->notify->getStatus());
        $this->assertEquals('授權成功', $this->notify->getMessage());
    }

    public function testVerifyWithInvalidTradeSha(): void
    {
        $mockData = [
            'Status' => 'SUCCESS',
            'Message' => '授權成功',
        ];

        $tradeInfo = $this->aesEncoder->encrypt($mockData);

        $data = [
            'TradeInfo' => $tradeInfo,
            'TradeSha' => 'invalid_sha',
            'MerchantID' => self::TEST_MERCHANT_ID,
        ];

        $result = $this->notify->verify($data);

        $this->assertFalse($result);
        $this->assertFalse($this->notify->isVerified());
    }

    public function testVerifyWithMissingFields(): void
    {
        $data = [
            'MerchantID' => self::TEST_MERCHANT_ID,
        ];

        $result = $this->notify->verify($data);

        $this->assertFalse($result);
    }

    public function testVerifyOrFailWithValidData(): void
    {
        $mockData = [
            'Status' => 'SUCCESS',
            'Message' => '授權成功',
        ];

        $tradeInfo = $this->aesEncoder->encrypt($mockData);
        $tradeSha = $this->checkValueEncoder->generate($tradeInfo);

        $data = [
            'TradeInfo' => $tradeInfo,
            'TradeSha' => $tradeSha,
        ];

        $notify = $this->notify->verifyOrFail($data);

        $this->assertInstanceOf(PaymentNotify::class, $notify);
    }

    public function testVerifyOrFailWithInvalidData(): void
    {
        $this->expectException(NewebPayException::class);

        $data = [
            'TradeInfo' => 'invalid',
            'TradeSha' => 'invalid',
        ];

        $this->notify->verifyOrFail($data);
    }

    public function testGetResultFields(): void
    {
        $mockData = [
            'Status' => 'SUCCESS',
            'Message' => '授權成功',
            'MerchantID' => self::TEST_MERCHANT_ID,
            'Result' => [
                'MerchantOrderNo' => 'TEST123456',
                'TradeNo' => '20231231000001',
                'Amt' => 1000,
                'PaymentType' => 'CREDIT',
                'PayTime' => '2023-12-31 12:00:00',
                'IP' => '127.0.0.1',
                'Auth' => '123456',
                'Card4No' => '1234',
                'Card6No' => '123456',
                'ECI' => '1',
                'Inst' => 3,
                'InstFirst' => 334,
                'InstEach' => 333,
            ],
        ];

        $tradeInfo = $this->aesEncoder->encrypt($mockData);
        $tradeSha = $this->checkValueEncoder->generate($tradeInfo);

        $this->notify->verify([
            'TradeInfo' => $tradeInfo,
            'TradeSha' => $tradeSha,
        ]);

        $this->assertEquals(self::TEST_MERCHANT_ID, $this->notify->getMerchantID());
        $this->assertEquals('TEST123456', $this->notify->getMerchantOrderNo());
        $this->assertEquals('20231231000001', $this->notify->getTradeNo());
        $this->assertEquals(1000, $this->notify->getAmt());
        $this->assertEquals('CREDIT', $this->notify->getPaymentType());
        $this->assertEquals('2023-12-31 12:00:00', $this->notify->getPayTime());
        $this->assertEquals('127.0.0.1', $this->notify->getIP());
        $this->assertEquals('123456', $this->notify->getAuthCode());
        $this->assertEquals('1234', $this->notify->getCard4No());
        $this->assertEquals('123456', $this->notify->getCard6No());
        $this->assertEquals('1', $this->notify->getECI());
        $this->assertEquals(3, $this->notify->getInst());
        $this->assertEquals(334, $this->notify->getInstFirst());
        $this->assertEquals(333, $this->notify->getInstEach());
    }

    public function testCreate(): void
    {
        $notify = PaymentNotify::create(self::TEST_HASH_KEY, self::TEST_HASH_IV);

        $this->assertInstanceOf(PaymentNotify::class, $notify);
    }
}
