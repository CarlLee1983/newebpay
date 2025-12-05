<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Laravel;

use CarlLee\NewebPay\Laravel\Services\PaymentBuilder;
use Illuminate\Http\Response;

/**
 * 內部的 Builder，用於攔截 submit
 */
class RecordingPaymentBuilder extends PaymentBuilder
{
    private ?NewebPayFake $fake = null;

    public function setFake(NewebPayFake $fake): void
    {
        $this->fake = $fake;
    }

    public function submit(): Response
    {
        if ($this->fake) {
            $this->fake->record($this->build());
        }

        return new Response('<html><body>Fake Payment Form</body></html>');
    }
}
