<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Laravel;

use CarlLee\NewebPay\Content;
use CarlLee\NewebPay\FormBuilder;
use CarlLee\NewebPay\Laravel\Services\PaymentBuilder;
use CarlLee\NewebPay\Laravel\Services\PaymentCoordinator;
use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Testing\Fakes\Fake;
use PHPUnit\Framework\Assert as PHPUnit;

class NewebPayFake extends PaymentCoordinator implements Fake
{
    /**
     * The payments that have been created.
     *
     * @var array<int, Content>
     */
    protected array $payments = [];

    /**
     * Create a new fake instance.
     */
    public function __construct()
    {
        parent::__construct('TEST_MERCHANT', 'TEST_KEY', 'TEST_IV', true);
    }

    /**
     * Assert if a payment was sent based on a truth-test callback.
     */
    public function assertSent(callable $callback): void
    {
        PHPUnit::assertTrue(
            $this->payments()->filter(function ($payment) use ($callback) {
                return $callback($payment);
            })->count() > 0,
            'The expected payment was not sent.'
        );
    }

    /**
     * Assert if a payment was not sent based on a truth-test callback.
     */
    public function assertNotSent(callable $callback): void
    {
        PHPUnit::assertTrue(
            $this->payments()->filter(function ($payment) use ($callback) {
                return $callback($payment);
            })->count() === 0,
            'The unexpected payment was sent.'
        );
    }

    /**
     * Assert that a given number of payments were sent.
     */
    public function assertSentCount(int $count): void
    {
        PHPUnit::assertCount(
            $count,
            $this->payments,
            "Expected {$count} payments to be sent, but received " . count($this->payments) . '.'
        );
    }

    /**
     * Get all of the payments matching a truth-test callback.
     */
    public function sent(callable $callback): Collection
    {
        return $this->payments()->filter(function ($payment) use ($callback) {
            return $callback($payment);
        });
    }

    /**
     * Get a collection of the payments.
     */
    public function payments(): Collection
    {
        return collect($this->payments);
    }

    /**
     * 覆寫 payment 方法以回傳 RecordingPaymentBuilder
     */
    public function payment(string $orderNo, int $amount, string $itemDesc, string $email = ''): PaymentBuilder
    {
        $builder = new RecordingPaymentBuilder(
            $this->merchantID,
            $this->hashKey,
            $this->hashIV,
            $this->isTest
        );

        // 將此 Fake 實例注入到 Builder 中，以便 Builder 回報
        $builder->setFake($this);

        return $builder->setOrder($orderNo, $amount, $itemDesc, $email);
    }

    /**
     * 覆寫 form 方法以捕捉透過 form builder 建立的交易
     */
    public function form($payment): FormBuilder
    {
        // 紀錄透過 NewebPay::form($payment)->build() 的交易
        // 但 build() 只是產生 HTML，不代表真的送出？
        // 為了方便測試，我們假設呼叫 form()...->build() 的意圖是為了送出
        // 這裡我們可以使用一個 FakeFormBuilder

        return new FakeFormBuilder($payment, $this);
    }

    /**
     * 供內部調用：紀錄一筆交易
     */
    public function record(Content $payment): void
    {
        $this->payments[] = $payment;
    }
}
