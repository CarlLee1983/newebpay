<?php

namespace CarlLee\NewebPay\Tests\Laravel;

use CarlLee\NewebPay\Laravel\Facades\NewebPay;
use CarlLee\NewebPay\Laravel\NewebPayServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class NewebPayFakeTest extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [NewebPayServiceProvider::class];
    }

    public function test_fake_swaps_instance()
    {
        NewebPay::fake();

        $this->assertInstanceOf(\CarlLee\NewebPay\Laravel\NewebPayFake::class, NewebPay::getFacadeRoot());
    }

    public function test_assert_sent()
    {
        NewebPay::fake();

        NewebPay::payment('ORDER_1', 100, 'Test Item', 'test@example.com')->submit();

        NewebPay::assertSent(function ($payment) {
            return $payment->get('MerchantOrderNo') === 'ORDER_1' &&
                   $payment->get('Amt') === 100;
        });
    }

    public function test_assert_not_sent()
    {
        NewebPay::fake();

        NewebPay::payment('ORDER_1', 100, 'Test Item', 'test@example.com')->submit();

        NewebPay::assertNotSent(function ($payment) {
            return $payment->get('MerchantOrderNo') === 'ORDER_2';
        });
    }

    public function test_assert_sent_count()
    {
        NewebPay::fake();

        NewebPay::payment('ORDER_1', 100, 'Test Item', 'test@example.com')->submit();
        NewebPay::payment('ORDER_2', 200, 'Test Item 2', 'test2@example.com')->submit();

        NewebPay::assertSentCount(2);
    }

    public function test_fake_form_builder()
    {
        NewebPay::fake();

        $payment = NewebPay::credit()
            ->setMerchantOrderNo('ORDER_3')
            ->setAmt(300)
            ->setItemDesc('Test Item 3')
            ->setEmail('test3@example.com');
        NewebPay::form($payment)->build();

        NewebPay::assertSent(function ($payment) {
            return $payment->get('MerchantOrderNo') === 'ORDER_3';
        });
    }
}
