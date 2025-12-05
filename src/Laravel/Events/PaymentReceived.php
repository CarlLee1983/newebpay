<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Laravel\Events;

use CarlLee\NewebPay\Notifications\PaymentNotify;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentReceived
{
    use Dispatchable;
    use SerializesModels;

    /**
     * 建立新事件實例。
     *
     * @param PaymentNotify $notify 支付通知實例
     */
    public function __construct(
        public PaymentNotify $notify
    ) {
    }
}
