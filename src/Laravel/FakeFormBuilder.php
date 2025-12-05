<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Laravel;

use CarlLee\NewebPay\FormBuilder;

/**
 * 內部的 FormBuilder，用於攔截 build
 */
class FakeFormBuilder extends FormBuilder
{
    private NewebPayFake $fake;
    private $payment;

    public function __construct($payment, NewebPayFake $fake)
    {
        parent::__construct($payment);
        $this->fake = $fake;
        $this->payment = $payment;
    }

    public function build(): string
    {
        $this->fake->record($this->payment);

        return '<form>Fake Payment Form</form>';
    }
}
