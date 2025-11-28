<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Laravel;

use CarlLee\NewebPay\Laravel\Services\PaymentBuilder;
use CarlLee\NewebPay\Operations\AtmPayment;
use CarlLee\NewebPay\Operations\CreditInstallment;
use CarlLee\NewebPay\Operations\CreditPayment;
use CarlLee\NewebPay\Operations\CvsPayment;
use CarlLee\NewebPay\Operations\LinePayPayment;
use CarlLee\NewebPay\Tests\TestCase;
use Illuminate\Http\Response;

/**
 * PaymentBuilder 測試。
 */
class PaymentBuilderTest extends TestCase
{
    private PaymentBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new PaymentBuilder(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV,
            true
        );
    }

    public function testSetOrder(): void
    {
        $this->builder->setOrder('ORDER123', 1000, '測試商品', 'test@example.com');

        $payment = $this->builder->build();

        $this->assertInstanceOf(CreditPayment::class, $payment);

        $content = $payment->getRawContent();
        $this->assertEquals('ORDER123', $content['MerchantOrderNo']);
        $this->assertEquals(1000, $content['Amt']);
        $this->assertEquals('測試商品', $content['ItemDesc']);
        $this->assertEquals('test@example.com', $content['Email']);
    }

    public function testDefaultPaymentIsCreditCard(): void
    {
        $this->builder->setOrder('ORDER123', 1000, '測試商品');

        $payment = $this->builder->build();

        $this->assertInstanceOf(CreditPayment::class, $payment);
    }

    public function testCreditCard(): void
    {
        $this->builder
            ->setOrder('ORDER123', 1000, '測試商品')
            ->creditCard();

        $payment = $this->builder->build();

        $this->assertInstanceOf(CreditPayment::class, $payment);
    }

    public function testCreditInstallment(): void
    {
        $this->builder
            ->setOrder('ORDER123', 3000, '測試商品')
            ->creditInstallment([3, 6, 12]);

        $payment = $this->builder->build();

        $this->assertInstanceOf(CreditInstallment::class, $payment);
    }

    public function testAtm(): void
    {
        $this->builder
            ->setOrder('ORDER123', 1000, '測試商品')
            ->atm('2025-12-31');

        $payment = $this->builder->build();

        $this->assertInstanceOf(AtmPayment::class, $payment);
    }

    public function testCvs(): void
    {
        $this->builder
            ->setOrder('ORDER123', 500, '測試商品')
            ->cvs();

        $payment = $this->builder->build();

        $this->assertInstanceOf(CvsPayment::class, $payment);
    }

    public function testLinePay(): void
    {
        $this->builder
            ->setOrder('ORDER123', 1000, '測試商品')
            ->linePay();

        $payment = $this->builder->build();

        $this->assertInstanceOf(LinePayPayment::class, $payment);
    }

    public function testReturnUrl(): void
    {
        $this->builder
            ->setOrder('ORDER123', 1000, '測試商品')
            ->returnUrl('https://example.com/return');

        $payment = $this->builder->build();
        $content = $payment->getRawContent();

        $this->assertEquals('https://example.com/return', $content['ReturnURL']);
    }

    public function testNotifyUrl(): void
    {
        $this->builder
            ->setOrder('ORDER123', 1000, '測試商品')
            ->notifyUrl('https://example.com/notify');

        $payment = $this->builder->build();
        $content = $payment->getRawContent();

        $this->assertEquals('https://example.com/notify', $content['NotifyURL']);
    }

    public function testCustomerUrl(): void
    {
        $this->builder
            ->setOrder('ORDER123', 1000, '測試商品')
            ->customerUrl('https://example.com/customer');

        $payment = $this->builder->build();
        $content = $payment->getRawContent();

        $this->assertEquals('https://example.com/customer', $content['CustomerURL']);
    }

    public function testClientBackUrl(): void
    {
        $this->builder
            ->setOrder('ORDER123', 1000, '測試商品')
            ->clientBackUrl('https://example.com/back');

        $payment = $this->builder->build();
        $content = $payment->getRawContent();

        $this->assertEquals('https://example.com/back', $content['ClientBackURL']);
    }

    public function testCustomize(): void
    {
        $this->builder
            ->setOrder('ORDER123', 1000, '測試商品')
            ->customize(function ($payment): void {
                $payment->setOrderComment('自訂備註');
            });

        $payment = $this->builder->build();
        $content = $payment->getRawContent();

        $this->assertEquals('自訂備註', $content['OrderComment']);
    }

    public function testGetHtml(): void
    {
        $this->builder
            ->setOrder('ORDER123', 1000, '測試商品')
            ->returnUrl('https://example.com/return')
            ->notifyUrl('https://example.com/notify');

        $html = $this->builder->getHtml();

        $this->assertStringContainsString('<form', $html);
        $this->assertStringContainsString('method="post"', $html);
        $this->assertStringContainsString('MerchantID', $html);
        $this->assertStringContainsString('TradeInfo', $html);
        $this->assertStringContainsString('TradeSha', $html);
    }

    public function testSubmit(): void
    {
        $this->builder
            ->setOrder('ORDER123', 1000, '測試商品')
            ->returnUrl('https://example.com/return')
            ->notifyUrl('https://example.com/notify');

        $response = $this->builder->submit();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertStringContainsString('<form', $response->getContent());
    }

    public function testGetParams(): void
    {
        $this->builder
            ->setOrder('ORDER123', 1000, '測試商品')
            ->returnUrl('https://example.com/return')
            ->notifyUrl('https://example.com/notify');

        $params = $this->builder->getParams();

        $this->assertArrayHasKey('action', $params);
        $this->assertArrayHasKey('method', $params);
        $this->assertArrayHasKey('fields', $params);
        $this->assertEquals('POST', $params['method']);
        $this->assertArrayHasKey('MerchantID', $params['fields']);
        $this->assertArrayHasKey('TradeInfo', $params['fields']);
        $this->assertArrayHasKey('TradeSha', $params['fields']);
    }

    public function testChainedCalls(): void
    {
        $response = (new PaymentBuilder(
            self::TEST_MERCHANT_ID,
            self::TEST_HASH_KEY,
            self::TEST_HASH_IV,
            true
        ))
            ->setOrder('ORDER123', 1000, '測試商品', 'test@example.com')
            ->creditCard()
            ->returnUrl('https://example.com/return')
            ->notifyUrl('https://example.com/notify')
            ->submit();

        $this->assertInstanceOf(Response::class, $response);
    }
}

