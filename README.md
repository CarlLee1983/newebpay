# 藍新金流 PHP SDK

[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.3-blue)](https://www.php.net)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)
[![Tests](https://github.com/CarlLee1983/newebpay/actions/workflows/tests.yml/badge.svg)](https://github.com/CarlLee1983/newebpay/actions/workflows/tests.yml)

藍新金流（NewebPay）PHP SDK，提供簡潔易用的 API 整合藍新金流支付服務。
# 藍新金流 (NewebPay) PHP SDK

<p align="center">
    <a href="https://packagist.org/packages/carllee1983/newebpay"><img src="https://img.shields.io/packagist/v/carllee1983/newebpay?style=flat-square&color=blue" alt="Latest Version"></a>
    <a href="https://packagist.org/packages/carllee1983/newebpay"><img src="https://img.shields.io/packagist/dt/carllee1983/newebpay?style=flat-square&color=green" alt="Total Downloads"></a>
    <a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-purple?style=flat-square" alt="License"></a>
    <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-%3E%3D7.4-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP Version"></a>
</p>

<p align="center">
    <strong>專為現代 PHP 開發者打造的藍新金流全方位整合方案</strong>
    <br>
    優雅的語法 • 完整的 Type Hinting • Laravel 深度整合
</p>

---

## ✨ 核心特色

- 🚀 **全面支援**：涵蓋信用卡、ATM、超商代碼/條碼、LINE Pay、台灣 Pay、Apple Pay 等主流支付。
- 🛡️ **安全可靠**：內建完整的 AES-256-CBC 加解密驗證機制，確保交易安全。
- 💎 **Laravel 整合**：提供 Service Provider 與 Facades，與 Laravel 生態系完美融合。
- 📦 **開箱即用**：簡單直覺的 Fluent API 設計，讓參數設定變得清晰易讀。
- ✅ **品質保證**：高覆蓋率的單元測試 (100+ tests)，確保每次交易都精確無誤。

## 📋 系統需求與相容性

本套件支援 PHP 7.4 及以上版本，並針對各 Laravel 版本進行了最佳化：

| Laravel 版本 | PHP 最低需求 | 支援狀態 |
|:---:|:---:|:---:|
| **Laravel 11.x** | PHP 8.2+ | ✅ 完美支援 |
| **Laravel 10.x** | PHP 8.1+ | ✅ 完美支援 |
| **Laravel 9.x** | PHP 8.0+ | ✅ 完美支援 |
| **Laravel 8.x** | PHP 7.4+ | ✅ 完美支援 |

## 🚀 快速安裝

使用 Composer 即可輕鬆安裝：

```bash
composer require carllee1983/newebpay
```

## 📖 快速上手

我們提供兩種使用風格，您可自由選擇最適合的一種。

### ⚡ 風格一：Laravel Facade (推薦)

最簡潔的現代化寫法，適合 Laravel 開發者。

**1. 設定環境變數 (.env)**
```env
NEWEBPAY_MERCHANT_ID=您的特店編號
NEWEBPAY_HASH_KEY=您的HashKey
NEWEBPAY_HASH_IV=您的HashIV
NEWEBPAY_TEST_MODE=true
NEWEBPAY_RETURN_URL=https://your-site.com/payment/return
NEWEBPAY_NOTIFY_URL=https://your-site.com/payment/notify
```

**2. 建立交易**
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

**進階用法 (指定支付方式)**
```php
NewebPay::payment($orderNo, $amount, $desc, $email)
    ->creditInstallment([3, 6]) // 僅開放 3, 6 期分期
    ->atm('2025-12-31')         // 指定 ATM 繳費期限
    ->linePay()                 // 啟用 LINE Pay
    ->submit();
```

### 🛠️ 風格二：原生 PHP 物件導向

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

## 💳 支援支付方式一覽

| 用途 | 類別 | 對應方法 | 備註 |
|:---|:---|:---|:---|
| **信用卡一次付清** | `CreditPayment` | `->creditCard()` | 預設啟用 |
| **信用卡分期** | `CreditInstallment` | `->creditInstallment()` | 支援 3/6/12/18/24/30 期 |
| **WebATM** | `WebAtmPayment` | `->webAtm()` | 需搭配讀卡機 |
| **ATM 轉帳** | `AtmPayment` | `->atm()` | 產生虛擬帳號 |
| **超商代碼** | `CvsPayment` | `->cvs()` | Kiosk 操作列印 |
| **超商條碼** | `BarcodePayment` | `->barcode()` | 手機出示條碼 |
| **LINE Pay** | `LinePayPayment` | `->linePay()` | 行動支付 |
| **玉山 Wallet** | `EsunWalletPayment` | `->esunWallet()` | 電子錢包 |
| **台灣 Pay** | `TaiwanPayPayment` | `->taiwanPay()` | 行動支付 |
| **BitoPay** | `BitoPayPayment` | `->bitoPay()` | 加密貨幣支付 |
| **超商取貨付款** | `CvscomPayment` | `->cvscom()` | 物流整合 |
| **Fula 付啦** | `FulaPayment` | `->fula()` | BNPL 先買後付 |
| **TWQR** | `TwqrPayment` | `->twqr()` | 通用 QR Code |
| **全支付方式** | `AllInOnePayment` | `->allInOne()` | 一次啟用多種選擇 |

*(完整列表請參閱 [Wiki](wiki_link_here) 或原始碼)*

## 🔔 處理回調 (Webhook)

當交易狀態變更時，藍新金流會通知您的伺服器。SDK 提供了優雅的封裝來驗證這些請求。

```php
use CarlLee\NewebPay\Notifications\PaymentNotify;

$notify = new PaymentNotify('HashKey', 'HashIV');

try {
    // 1. 自動驗證簽章與解密 (若驗證失敗會拋出例外)
    $data = $notify->verifyOrFail($_POST);
    
    // 2. 判斷交易結果
    if ($notify->isSuccess()) {
        // 交易成功！
        $orderId = $notify->getMerchantOrderNo();
        $amount = $notify->getAmt();
        
        // TODO: 更新資料庫訂單狀態...
    } else {
        // 交易失敗 (刷卡失敗、餘額不足等)
    }
    
} catch (\Exception $e) {
    // 簽章驗證失敗，可能是偽造的請求
    Log::error('Payment notify verification failed: ' . $e->getMessage());
}
```

## 📢 事件監聽 (Events)

在 Laravel 應用程式中，您也可以透過監聽事件來處理支付結果，讓程式碼更乾淨解耦。

**1. 定義監聽器**
```php
namespace App\Listeners;

use CarlLee\NewebPay\Laravel\Events\PaymentReceived;

class HandlePaymentReceived
{
    public function handle(PaymentReceived $event)
    {
        $notify = $event->notify;
        
        if ($notify->isSuccess()) {
            // 處理付款成功邏輯
            $orderId = $notify->getMerchantOrderNo();
            // ...
        }
    }
}
```

**2. 註冊監聽器 (EventServiceProvider)**
```php
protected $listen = [
    \CarlLee\NewebPay\Laravel\Events\PaymentReceived::class => [
        \App\Listeners\HandlePaymentReceived::class,
    ],
];
```

## 🧪 測試 (Testing)

我們提供了 `NewebPay::fake()` 讓您在測試中輕鬆模擬支付請求，無需實際發送 HTTP 請求。

```php
use CarlLee\NewebPay\Laravel\Facades\NewebPay;

public function test_payment_flow()
{
    // 1. 啟用模擬模式
    NewebPay::fake();

    // 2. 執行您的程式碼
    $this->post('/checkout');

    // 3. 驗證是否建立了正確的支付請求
    NewebPay::assertSent(function ($payment) {
        return $payment->get('Amt') === 1000 &&
               $payment->get('Email') === 'buyer@example.com';
    });
}
```

## 🔎 交易查詢與退款

**查詢訂單**
```php
use CarlLee\NewebPay\Queries\QueryOrder;

$result = QueryOrder::create($id, $key, $iv)
    ->query('ORDER_NO_12345', 1000); // 需帶入訂單編號與金額
    
echo $result['TradeStatus']; // 1=成功, 0=未付款...
```

**信用卡退款**
```php
use CarlLee\NewebPay\Actions\CreditClose;

CreditClose::create($id, $key, $iv)
    ->refund('ORDER_NO_12345', 1000); // 全額退款
```

## 💻 前後端分離整合 (Vue / React)

由於藍新金流需要 `Form Post` 跳轉，在 SPA (Single Page Application) 中，建議由後端產生 API 回傳表單參數，前端再動態建立表單提交。

**後端 (Laravel Example)**
```php
public function checkout() {
    $payment = NewebPay::credit()->...; // 設定參數
    
    return response()->json([
        'url' => $payment->getApiUrl(),
        'fields' => $payment->getContent() // 取得所有加密後的隱藏欄位
    ]);
}
```

**前端 (Javascript Example)**
```javascript
// 取得後端參數後...
const form = document.createElement('form');
form.method = 'POST';
form.action = response.url;

for (const [key, value] of Object.entries(response.fields)) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = key;
    input.value = value;
    form.appendChild(input);
}

document.body.appendChild(form);
form.submit();
```

## 🐳 Docker 開發環境

為了確保環境一致性，我們提供了完整的 Docker 開發環境配置。

```bash
make build           # 建構環境
make composer-install # 安裝套件
make test            # 執行測試
```

## 📄 授權協議

本專案採用 **MIT License** 開源授權，您可以安心使用於商業專案中。
