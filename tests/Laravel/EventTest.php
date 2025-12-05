<?php

namespace CarlLee\NewebPay\Tests\Laravel;

use CarlLee\NewebPay\Laravel\Events\PaymentReceived;
use CarlLee\NewebPay\Laravel\NewebPayServiceProvider;
use CarlLee\NewebPay\Laravel\Notifications\LaravelPaymentNotify;
use Illuminate\Support\Facades\Event;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Mockery;

class EventTest extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [NewebPayServiceProvider::class];
    }

    public function test_payment_received_event_is_dispatched()
    {
        Event::fake();

        $key = '12345678901234567890123456789012';
        $iv = '1234567890123456';

        $notify = new LaravelPaymentNotify($key, $iv);
        $notify->setDispatcher(app('events'));

        // Generate valid data
        $aes = new \CarlLee\NewebPay\Infrastructure\AES256Encoder($key, $iv);
        $check = new \CarlLee\NewebPay\Infrastructure\CheckValueEncoder($key, $iv);

        $tradeInfo = $aes->encrypt(['Status' => 'SUCCESS', 'Message' => 'Test']);
        $tradeSha = $check->generate($tradeInfo);

        $data = [
            'TradeInfo' => $tradeInfo,
            'TradeSha' => $tradeSha,
        ];

        $notify->verify($data);

        Event::assertDispatched(PaymentReceived::class, function ($event) use ($notify) {
            return $event->notify === $notify;
        });
    }

    public function test_service_provider_binds_correct_class()
    {
        $notify = $this->app->make('newebpay.notify');
        $this->assertInstanceOf(LaravelPaymentNotify::class, $notify);
    }
}
