<?php

namespace CarlLee\NewebPay\Laravel\Notifications;

use CarlLee\NewebPay\Laravel\Events\PaymentReceived;
use CarlLee\NewebPay\Notifications\PaymentNotify;
use Illuminate\Contracts\Events\Dispatcher;

class LaravelPaymentNotify extends PaymentNotify
{
    protected ?Dispatcher $dispatcher = null;

    public function setDispatcher(Dispatcher $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }

    public function verify(array $data): bool
    {
        $result = parent::verify($data);

        if ($this->dispatcher && $result) {
            $this->dispatcher->dispatch(new PaymentReceived($this));
        }

        return $result;
    }
}
