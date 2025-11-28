<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Notifications;

use CarlLee\NewebPay\Infrastructure\AES256Encoder;
use CarlLee\NewebPay\Infrastructure\CheckValueEncoder;
use CarlLee\NewebPay\Notifications\AtmNotify;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * ATM 取號通知處理器測試。
 */
class AtmNotifyTest extends TestCase
{
    private AtmNotify $notify;
    private AES256Encoder $aesEncoder;
    private CheckValueEncoder $checkValueEncoder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->notify = new AtmNotify(
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

    public function testGetAtmFields(): void
    {
        $mockData = [
            'Status' => 'SUCCESS',
            'Message' => '取號成功',
            'Result' => [
                'MerchantOrderNo' => 'ATM123456',
                'TradeNo' => '20231231000001',
                'Amt' => 2000,
                'PaymentType' => 'VACC',
                'BankCode' => '004',
                'CodeNo' => '1234567890123456',
                'ExpireDate' => '2024-01-07',
                'ExpireTime' => '23:59:59',
            ],
        ];

        $tradeInfo = $this->aesEncoder->encrypt($mockData);
        $tradeSha = $this->checkValueEncoder->generate($tradeInfo);

        $this->notify->verify([
            'TradeInfo' => $tradeInfo,
            'TradeSha' => $tradeSha,
        ]);

        $this->assertEquals('004', $this->notify->getBankCode());
        $this->assertEquals('1234567890123456', $this->notify->getCodeNo());
        $this->assertEquals('2024-01-07', $this->notify->getExpireDate());
        $this->assertEquals('23:59:59', $this->notify->getExpireTime());
    }

    public function testEmptyFields(): void
    {
        $mockData = [
            'Status' => 'SUCCESS',
            'Message' => '取號成功',
            'Result' => [],
        ];

        $tradeInfo = $this->aesEncoder->encrypt($mockData);
        $tradeSha = $this->checkValueEncoder->generate($tradeInfo);

        $this->notify->verify([
            'TradeInfo' => $tradeInfo,
            'TradeSha' => $tradeSha,
        ]);

        $this->assertEquals('', $this->notify->getBankCode());
        $this->assertEquals('', $this->notify->getCodeNo());
        $this->assertEquals('', $this->notify->getExpireDate());
        $this->assertEquals('', $this->notify->getExpireTime());
    }
}
