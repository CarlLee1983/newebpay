<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Notifications;

use CarlLee\NewebPay\Infrastructure\AES256Encoder;
use CarlLee\NewebPay\Infrastructure\CheckValueEncoder;
use CarlLee\NewebPay\Notifications\CvsNotify;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * 超商取號通知處理器測試。
 */
class CvsNotifyTest extends TestCase
{
    private CvsNotify $notify;
    private AES256Encoder $aesEncoder;
    private CheckValueEncoder $checkValueEncoder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->notify = new CvsNotify(
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

    public function testGetCvsFields(): void
    {
        $mockData = [
            'Status' => 'SUCCESS',
            'Message' => '取號成功',
            'Result' => [
                'MerchantOrderNo' => 'CVS123456',
                'TradeNo' => '20231231000001',
                'Amt' => 500,
                'PaymentType' => 'CVS',
                'CodeNo' => 'ABC123456789',
                'StoreType' => '全家',
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

        $this->assertEquals('ABC123456789', $this->notify->getCodeNo());
        $this->assertEquals('全家', $this->notify->getStoreType());
        $this->assertEquals('2024-01-07', $this->notify->getExpireDate());
        $this->assertEquals('23:59:59', $this->notify->getExpireTime());
    }

    public function testGetBarcodeFields(): void
    {
        $mockData = [
            'Status' => 'SUCCESS',
            'Message' => '取號成功',
            'Result' => [
                'MerchantOrderNo' => 'BARCODE123',
                'Amt' => 1000,
                'PaymentType' => 'BARCODE',
                'Barcode_1' => '12345',
                'Barcode_2' => '67890',
                'Barcode_3' => '11111',
                'ExpireDate' => '2024-01-10',
            ],
        ];

        $tradeInfo = $this->aesEncoder->encrypt($mockData);
        $tradeSha = $this->checkValueEncoder->generate($tradeInfo);

        $this->notify->verify([
            'TradeInfo' => $tradeInfo,
            'TradeSha' => $tradeSha,
        ]);

        $this->assertEquals('12345', $this->notify->getBarcode1());
        $this->assertEquals('67890', $this->notify->getBarcode2());
        $this->assertEquals('11111', $this->notify->getBarcode3());
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

        $this->assertEquals('', $this->notify->getCodeNo());
        $this->assertEquals('', $this->notify->getStoreType());
        $this->assertEquals('', $this->notify->getBarcode1());
        $this->assertEquals('', $this->notify->getBarcode2());
        $this->assertEquals('', $this->notify->getBarcode3());
    }
}
