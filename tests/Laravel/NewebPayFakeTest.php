<?php

namespace CarlLee\NewebPay\Tests\Laravel;

use CarlLee\NewebPay\Laravel\Facades\NewebPay;
use CarlLee\NewebPay\Tests\TestCase;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use CarlLee\NewebPay\Laravel\NewebPayServiceProvider;

class NewebPayFakeTest extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [NewebPayServiceProvider::class];
    }

    public function test_fake_swaps_implementation()
    {
        $fake = NewebPay::fake();

        $this->assertInstanceOf(\CarlLee\NewebPay\Laravel\NewebPayFake::class, $fake);
        $this->assertInstanceOf(\CarlLee\NewebPay\Laravel\NewebPayFake::class, NewebPay::getFacadeRoot());
    }

    public function test_fake_assert_sent_payment_via_simplified_api()
    {
        NewebPay::fake();

        NewebPay::payment('ORDER123', 1000, 'Test Item', 'test@example.com')->submit();

        NewebPay::assertSent(function ($payment) {
            return $payment->get('MerchantOrderNo') === 'ORDER123' &&
                   $payment->get('Amt') === 1000 &&
                   $payment->get('Email') === 'test@example.com';
        });
    }

    public function test_fake_assert_sent_payment_via_form_builder()
    {
        NewebPay::fake();

        $payment = NewebPay::credit()
            ->setMerchantOrderNo('ORDER456')
            ->setAmt(2000)
            ->setItemDesc('Test Credit');
        
        NewebPay::form($payment)->build();

        NewebPay::assertSent(function ($payment) {
            return $payment->get('MerchantOrderNo') === 'ORDER456' &&
                   $payment->get('Amt') === 2000;
        });
    }

    public function test_fake_assert_not_sent()
    {
        NewebPay::fake();

        NewebPay::payment('ORDER123', 1000, 'Test', 'test@example.com')->submit();

        NewebPay::assertNotSent(function ($payment) {
            return $payment->get('MerchantOrderNo') === 'ORDER999';
        });
    }

    public function test_fake_assert_sent_count()
    {
        NewebPay::fake();

        NewebPay::payment('ORDER1', 100, 'T1', 't1@e.com')->submit();
        NewebPay::payment('ORDER2', 200, 'T2', 't2@e.com')->submit();

        NewebPay::assertSentCount(2);
    }
}
