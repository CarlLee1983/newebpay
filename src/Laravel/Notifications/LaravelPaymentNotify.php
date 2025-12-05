<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Laravel\Notifications;

use CarlLee\NewebPay\Laravel\Events\PaymentReceived;
use CarlLee\NewebPay\Notifications\PaymentNotify;
use Illuminate\Contracts\Events\Dispatcher;

class LaravelPaymentNotify extends PaymentNotify
{
    /**
     * 事件發送器。
     */
    private ?Dispatcher $dispatcher = null;

    /**
     * 設定事件發送器。
     */
    public function setDispatcher(Dispatcher $dispatcher): static
    {
        $this->dispatcher = $dispatcher;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function verify(array $data): bool
    {
        $result = parent::verify($data);

        if ($result && $this->dispatcher) {
            $this->dispatcher->dispatch(new PaymentReceived($this));
        }

        return $result;
    }
}
