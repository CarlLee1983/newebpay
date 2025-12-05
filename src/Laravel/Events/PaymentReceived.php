<?php

namespace CarlLee\NewebPay\Laravel\Events;

use CarlLee\NewebPay\Laravel\Notifications\LaravelPaymentNotify;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentReceived
{
    use Dispatchable;
    use SerializesModels;

    public LaravelPaymentNotify $notify;

    public function __construct(LaravelPaymentNotify $notify)
    {
        $this->notify = $notify;
    }
}
