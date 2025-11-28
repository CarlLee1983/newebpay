<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit;

use CarlLee\NewebPay\FormBuilder;
use CarlLee\NewebPay\Operations\CreditPayment;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * 表單產生器測試。
 */
class FormBuilderTest extends TestCase
{
    private CreditPayment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->payment = new CreditPayment(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV
        );

        $this->payment
            ->setMerchantOrderNo('TEST123456')
            ->setAmt(1000)
            ->setItemDesc('測試商品')
            ->setReturnURL('https://example.com/return')
            ->setTestMode(true);
    }

    public function testBuildForm(): void
    {
        $builder = new FormBuilder($this->payment);
        $html = $builder->build();

        // 檢查表單元素
        $this->assertStringContainsString('<form', $html);
        $this->assertStringContainsString('</form>', $html);
        $this->assertStringContainsString('method="post"', $html);
        $this->assertStringContainsString('action="https://ccore.newebpay.com/MPG/mpg_gateway"', $html);

        // 檢查隱藏欄位
        $this->assertStringContainsString('name="MerchantID"', $html);
        $this->assertStringContainsString('name="TradeInfo"', $html);
        $this->assertStringContainsString('name="TradeSha"', $html);
        $this->assertStringContainsString('name="Version"', $html);
    }

    public function testBuildFormWithAutoSubmit(): void
    {
        $builder = new FormBuilder($this->payment);
        $builder->setAutoSubmit(true);

        $html = $builder->build();

        // 應包含自動送出 JavaScript
        $this->assertStringContainsString('<script>', $html);
        $this->assertStringContainsString('.submit()', $html);
    }

    public function testBuildFormWithoutAutoSubmit(): void
    {
        $builder = new FormBuilder($this->payment);
        $builder->setAutoSubmit(false);

        $html = $builder->build();

        // 不應包含自動送出 JavaScript
        $this->assertStringNotContainsString('<script>', $html);
    }

    public function testSetFormId(): void
    {
        $builder = new FormBuilder($this->payment);
        $builder->setFormId('my-payment-form');

        $html = $builder->build();

        $this->assertStringContainsString('id="my-payment-form"', $html);
    }

    public function testSetSubmitText(): void
    {
        $builder = new FormBuilder($this->payment);
        $builder->setAutoSubmit(false);
        $builder->setSubmitText('立即付款');

        $html = $builder->build();

        $this->assertStringContainsString('立即付款', $html);
    }

    public function testCreate(): void
    {
        $builder = FormBuilder::create($this->payment);

        $this->assertInstanceOf(FormBuilder::class, $builder);
    }

    public function testToString(): void
    {
        $builder = new FormBuilder($this->payment);

        $html = (string) $builder;

        $this->assertStringContainsString('<form', $html);
    }
}
