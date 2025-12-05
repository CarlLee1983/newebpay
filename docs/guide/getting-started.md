# 快速上手

我們提供兩種使用風格，您可自由選擇最適合的一種。

## 風格一：Laravel Facade (推薦)

最簡潔的現代化寫法，適合 Laravel 開發者。

### 1. 設定環境變數 (.env)

```env
NEWEBPAY_MERCHANT_ID=您的特店編號
NEWEBPAY_HASH_KEY=您的HashKey
NEWEBPAY_HASH_IV=您的HashIV
NEWEBPAY_TEST_MODE=true
NEWEBPAY_RETURN_URL=https://your-site.com/payment/return
NEWEBPAY_NOTIFY_URL=https://your-site.com/payment/notify
```

### 2. 建立交易

```php
use CarlLee\NewebPay\Laravel\Facades\NewebPay;

Route::post('/pay', function () {
    return NewebPay::payment(
        'ORDER_' . time(),  // 訂單編號
        1000,               // 金額
        '測試商品',          // 商品描述
        'user@example.com'  // 買家 Email
    )->submit();
});
```

### 進階用法 (指定支付方式)

```php
NewebPay::payment($orderNo, $amount, $desc, $email)
    ->creditInstallment([3, 6]) // 僅開放 3, 6 期分期
    ->atm('2025-12-31')         // 指定 ATM 繳費期限
    ->linePay()                 // 啟用 LINE Pay
    ->submit();
```

## 風格二：原生 PHP 物件導向

適合非 Laravel 專案或需要細膩控制時使用。

```php
use CarlLee\NewebPay\Operations\CreditPayment;
use CarlLee\NewebPay\FormBuilder;

// 初始化
$payment = new CreditPayment('MerchantID', 'HashKey', 'HashIV');

// 設定參數
$payment->setTestMode(true)
        ->setMerchantOrderNo('ORDER_' . time())
        ->setAmt(1000)
        ->setItemDesc('商品名稱')
        ->setEmail('buyer@example.com')
        ->setReturnURL('https://site.com/return')
        ->setNotifyURL('https://site.com/notify');

// 產生 HTML 表單並送出
echo FormBuilder::create($payment)->build();
```
