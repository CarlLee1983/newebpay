<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Operations;

use CarlLee\NewebPay\Operations\EsunWalletPayment;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * 玉山 Wallet 支付測試。
 */
class EsunWalletPaymentTest extends TestCase
{
    private EsunWalletPayment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->payment = new EsunWalletPayment(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );
    }

    public function testEsunWalletPaymentHasEsunWalletEnabled(): void
    {
        $content = $this->payment->getRawContent();

        $this->assertEquals(1, $content['ESUNWALLET']);
    }
}
