<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Laravel;

use CarlLee\NewebPay\Laravel\Services\PaymentCoordinator;
use CarlLee\NewebPay\Notifications\PaymentNotify;
use Illuminate\Support\ServiceProvider;

/**
 * 藍新金流 Laravel Service Provider。
 */
class NewebPayServiceProvider extends ServiceProvider
{
    /**
     * 註冊服務。
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/newebpay.php',
            'newebpay'
        );

        $this->registerPaymentCoordinator();
        $this->registerPaymentNotify();
    }

    /**
     * 啟動服務。
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/newebpay.php' => config_path('newebpay.php'),
            ], 'newebpay-config');
        }
    }

    /**
     * 註冊 PaymentCoordinator。
     *
     * @return void
     */
    protected function registerPaymentCoordinator(): void
    {
        $this->app->singleton(PaymentCoordinator::class, function ($app) {
            $config = $app['config']['newebpay'];

            return new PaymentCoordinator(
                $config['merchant_id'] ?? '',
                $config['hash_key'] ?? '',
                $config['hash_iv'] ?? '',
                $config['test_mode'] ?? true
            );
        });

        $this->app->alias(PaymentCoordinator::class, 'newebpay');
    }

    /**
     * 註冊 PaymentNotify。
     *
     * @return void
     */
    protected function registerPaymentNotify(): void
    {
        $this->app->singleton('newebpay.notify', function ($app) {
            $config = $app['config']['newebpay'];

            return new PaymentNotify(
                $config['hash_key'] ?? '',
                $config['hash_iv'] ?? ''
            );
        });
    }

    /**
     * 取得提供的服務。
     *
     * @return array<string>
     */
    public function provides(): array
    {
        return [
            PaymentCoordinator::class,
            'newebpay',
            'newebpay.notify',
        ];
    }
}
