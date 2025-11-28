# 藍新金流 PHP SDK

[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D7.4-blue)](https://www.php.net)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)

藍新金流（NewebPay）PHP SDK，提供簡潔易用的 API 整合藍新金流支付服務。

## 功能特色

- 支援全部支付方式：信用卡、ATM 轉帳、超商代碼/條碼繳費、LINE Pay、台灣 Pay 等
- 完整的 AES-256-CBC 加解密實作
- 支援交易查詢、退款、取消授權
- 提供 Laravel 整合（Service Provider、Facades）
- 完整的單元測試（149 tests, 312 assertions）
- PHP 7.4+ 支援

## 系統需求

- PHP 7.4 或更高版本
- OpenSSL 擴充套件
- JSON 擴充套件

## Laravel 版本支援

| Laravel 版本 | PHP 需求 | 支援狀態 |
|-------------|----------|----------|
| Laravel 8.x | PHP 7.3+ | ✅ 支援 |
| Laravel 9.x | PHP 8.0+ | ✅ 支援 |
| Laravel 10.x | PHP 8.1+ | ✅ 支援 |
| Laravel 11.x | PHP 8.2+ | ✅ 支援 |

> **注意**：您的 PHP 版本決定可用的 Laravel 版本。例如 PHP 7.4 僅能使用 Laravel 8.x。

## 安裝

```bash
composer require carllee1983/newebpay
```

## 快速開始

### 基本使用

```php
use CarlLee\NewebPay\Operations\CreditPayment;
use CarlLee\NewebPay\FormBuilder;

// 建立信用卡付款
$payment = new CreditPayment('特店編號', 'HashKey', 'HashIV');

$payment
    ->setTestMode(true)                                  // 測試環境
    ->setMerchantOrderNo('ORDER' . time())               // 訂單編號
    ->setAmt(1000)                                       // 金額
    ->setItemDesc('測試商品')                             // 商品描述
    ->setEmail('buyer@example.com')                      // 買家 Email
    ->setReturnURL('https://your-site.com/return')       // 付款完成返回網址
    ->setNotifyURL('https://your-site.com/notify');      // 付款結果通知網址

// 產生表單並自動送出
$form = FormBuilder::create($payment)->build();
echo $form;
```

### Laravel 整合

1. 發布設定檔：

```bash
php artisan vendor:publish --tag=newebpay-config
```

2. 設定環境變數（`.env`）：

```env
NEWEBPAY_TEST_MODE=true
NEWEBPAY_MERCHANT_ID=您的特店編號
NEWEBPAY_HASH_KEY=您的HashKey
NEWEBPAY_HASH_IV=您的HashIV
NEWEBPAY_RETURN_URL=https://your-site.com/payment/return
NEWEBPAY_NOTIFY_URL=https://your-site.com/payment/notify
```

3. 使用 Facade：

```php
use CarlLee\NewebPay\Laravel\Facades\NewebPay;

// 方式一：簡化 API（推薦）
Route::post('/pay', function () {
    $no = 'Vanespl_ec_' . time();
    $amt = 120;
    $desc = '我的商品';
    $email = 'test@example.com';

    return NewebPay::payment($no, $amt, $desc, $email)->submit();
});

// 方式二：指定支付方式
Route::post('/pay/atm', function () {
    return NewebPay::payment('ORDER' . time(), 1000, '商品', 'test@example.com')
        ->atm('2025-12-31')  // ATM 虛擬帳號，指定繳費期限
        ->submit();
});

// 方式三：完整控制
$payment = NewebPay::credit()
    ->setMerchantOrderNo('ORDER' . time())
    ->setAmt(1000)
    ->setItemDesc('測試商品');

$form = NewebPay::form($payment)->build();
```

### 簡化 API 支援的支付方式

```php
NewebPay::payment($no, $amt, $desc, $email)
    ->creditCard()           // 信用卡一次付清（預設）
    ->creditInstallment([3, 6, 12])  // 信用卡分期
    ->webAtm()               // WebATM
    ->atm('2025-12-31')      // ATM 虛擬帳號
    ->cvs('2025-12-31')      // 超商代碼
    ->barcode('2025-12-31')  // 超商條碼
    ->linePay()              // LINE Pay
    ->taiwanPay()            // 台灣 Pay
    ->allInOne()             // 全支付方式
    ->submit();              // 送出
```

## 支援的支付方式

| 支付方式 | 類別 | 說明 |
|---------|------|------|
| 信用卡一次付清 | `CreditPayment` | 支援紅利折抵、銀聯卡 |
| 信用卡分期 | `CreditInstallment` | 3/6/12/18/24/30 期 |
| WebATM | `WebAtmPayment` | 即時網路 ATM 轉帳 |
| ATM 轉帳 | `AtmPayment` | 虛擬帳號轉帳 |
| 超商代碼繳費 | `CvsPayment` | 金額限制 30~20,000 元 |
| 超商條碼繳費 | `BarcodePayment` | 金額限制 20~40,000 元 |
| LINE Pay | `LinePayPayment` | LINE Pay 電子錢包 |
| 台灣 Pay | `TaiwanPayPayment` | 台灣 Pay 行動支付 |
| 玉山 Wallet | `EsunWalletPayment` | 玉山銀行電子錢包 |
| BitoPay | `BitoPayPayment` | 加密貨幣支付 |
| TWQR | `TwqrPayment` | TWQR 共通支付 |
| 付啦 | `FulaPayment` | 先買後付 |
| 超商取貨付款 | `CvscomPayment` | 超商物流取貨付款 |
| 全支付方式 | `AllInOnePayment` | 自訂啟用多種支付 |

## 使用範例

### 信用卡分期

```php
use CarlLee\NewebPay\Operations\CreditInstallment;

$payment = new CreditInstallment('特店編號', 'HashKey', 'HashIV');

$payment
    ->setTestMode(true)
    ->setMerchantOrderNo('INST' . time())
    ->setAmt(3000)
    ->setItemDesc('分期商品')
    ->setInstallment([3, 6, 12])  // 提供 3/6/12 期選項
    ->setReturnURL('https://your-site.com/return')
    ->setNotifyURL('https://your-site.com/notify');
```

### ATM 虛擬帳號

```php
use CarlLee\NewebPay\Operations\AtmPayment;

$payment = new AtmPayment('特店編號', 'HashKey', 'HashIV');

$payment
    ->setTestMode(true)
    ->setMerchantOrderNo('ATM' . time())
    ->setAmt(2000)
    ->setItemDesc('ATM 轉帳測試')
    ->setExpireDate(date('Y-m-d', strtotime('+7 days')))  // 繳費期限
    ->setBankType(AtmPayment::BANK_BOT)                   // 指定銀行
    ->setReturnURL('https://your-site.com/return')
    ->setNotifyURL('https://your-site.com/notify')
    ->setCustomerURL('https://your-site.com/customer');   // 取號完成返回
```

### 全支付方式

```php
use CarlLee\NewebPay\Operations\AllInOnePayment;

$payment = new AllInOnePayment('特店編號', 'HashKey', 'HashIV');

$payment
    ->setTestMode(true)
    ->setMerchantOrderNo('ALL' . time())
    ->setAmt(1000)
    ->setItemDesc('多元支付測試')
    ->enableCredit()      // 啟用信用卡
    ->enableAtm()         // 啟用 ATM
    ->enableCvs()         // 啟用超商代碼
    ->enableLinePay()     // 啟用 LINE Pay
    ->setReturnURL('https://your-site.com/return')
    ->setNotifyURL('https://your-site.com/notify');
```

## 處理付款通知

### 支付完成通知

```php
use CarlLee\NewebPay\Notifications\PaymentNotify;

$notify = new PaymentNotify('HashKey', 'HashIV');

try {
    $notify->verifyOrFail($_POST);
    
    if ($notify->isSuccess()) {
        $orderNo = $notify->getMerchantOrderNo();
        $tradeNo = $notify->getTradeNo();
        $amount = $notify->getAmt();
        $paymentType = $notify->getPaymentType();
        
        // 信用卡額外資訊
        if ($paymentType === 'CREDIT') {
            $authCode = $notify->getAuthCode();
            $card4No = $notify->getCard4No();
        }
        
        // 更新訂單狀態...
    }
} catch (\Exception $e) {
    // 驗證失敗
}
```

### ATM 取號通知

```php
use CarlLee\NewebPay\Notifications\AtmNotify;

$notify = new AtmNotify('HashKey', 'HashIV');

if ($notify->verify($_POST) && $notify->isSuccess()) {
    $bankCode = $notify->getBankCode();      // 銀行代碼
    $codeNo = $notify->getCodeNo();          // 虛擬帳號
    $expireDate = $notify->getExpireDate();  // 繳費截止日
    
    // 儲存繳費資訊...
}
```

### 超商取號通知

```php
use CarlLee\NewebPay\Notifications\CvsNotify;

$notify = new CvsNotify('HashKey', 'HashIV');

if ($notify->verify($_POST) && $notify->isSuccess()) {
    $codeNo = $notify->getCodeNo();          // 繳費代碼
    $storeType = $notify->getStoreType();    // 超商類型
    $expireDate = $notify->getExpireDate();  // 繳費截止日
    
    // 條碼繳費
    $barcode1 = $notify->getBarcode1();
    $barcode2 = $notify->getBarcode2();
    $barcode3 = $notify->getBarcode3();
}
```

## 交易查詢

```php
use CarlLee\NewebPay\Queries\QueryOrder;

$query = QueryOrder::create('特店編號', 'HashKey', 'HashIV')
    ->setTestMode(true);

try {
    $result = $query->query('ORDER123456', 1000);
    
    echo "交易狀態：" . $result['TradeStatus'];
    echo "付款方式：" . $result['PaymentType'];
} catch (\Exception $e) {
    echo "查詢失敗：" . $e->getMessage();
}
```

## 退款與取消

### 信用卡退款

```php
use CarlLee\NewebPay\Actions\CreditClose;

$creditClose = CreditClose::create('特店編號', 'HashKey', 'HashIV')
    ->setTestMode(true);

// 退款
$result = $creditClose->refund('ORDER123456', 500);

// 請款（授權後請款）
$result = $creditClose->pay('ORDER123456', 1000);

// 取消請退款
$result = $creditClose->cancelClose('ORDER123456', 500, CreditClose::CLOSE_TYPE_REFUND);
```

### 取消授權

```php
use CarlLee\NewebPay\Actions\CreditCancel;

$creditCancel = CreditCancel::create('特店編號', 'HashKey', 'HashIV')
    ->setTestMode(true);

$result = $creditCancel->cancel('ORDER123456', 1000);
```

### 電子錢包退款

```php
use CarlLee\NewebPay\Actions\EWalletRefund;

$refund = EWalletRefund::create('特店編號', 'HashKey', 'HashIV')
    ->setTestMode(true);

$result = $refund->refund('ORDER123456', 500, 'LINEPAY');
```

## 前端框架整合（Vue / React）

藍新金流使用表單 POST 跳轉方式進行支付，前端框架需要透過後端 API 取得加密參數後組裝表單送出。

### 後端 API 範例

```php
// Laravel Controller
public function create(Request $request)
{
    $payment = NewebPay::credit()
        ->setMerchantOrderNo($request->order_id)
        ->setAmt($request->amount)
        ->setItemDesc($request->item_desc)
        ->setReturnURL(config('newebpay.return_url'))
        ->setNotifyURL(config('newebpay.notify_url'));

    return response()->json([
        'success' => true,
        'data' => [
            'action' => $payment->getApiUrl(),
            'method' => 'POST',
            'fields' => $payment->getContent(),
        ],
    ]);
}
```

### Vue 範例

```javascript
async function checkout() {
  var response = await fetch('/api/payment/create', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ order_id: 'xxx', amount: 1000 }),
  });
  var result = await response.json();

  // 建立表單並送出
  var form = document.createElement('form');
  form.method = result.data.method;
  form.action = result.data.action;
  Object.keys(result.data.fields).forEach(function(name) {
    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = result.data.fields[name];
    form.appendChild(input);
  });
  document.body.appendChild(form);
  form.submit();
}
```

### React 範例

```jsx
async function checkout() {
  var response = await fetch('/api/payment/create', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ order_id: 'xxx', amount: 1000 }),
  });
  var result = await response.json();

  var form = document.createElement('form');
  form.method = result.data.method;
  form.action = result.data.action;
  Object.keys(result.data.fields).forEach(function(name) {
    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = result.data.fields[name];
    form.appendChild(input);
  });
  document.body.appendChild(form);
  form.submit();
}
```

> 📖 完整範例請參閱 [examples/20-frontend-integration.md](examples/20-frontend-integration.md)

## Docker 開發環境

如果你的本機 PHP 版本與專案需求不符（例如本機 PHP 8.x，但需要在 PHP 7.4 下測試），可以使用 Docker 來建立一致的開發環境。

### 快速開始

```bash
# 建構 Docker 映像檔
make build

# 安裝 Composer 依賴
make composer-install

# 執行測試
make test
```

### 可用的 Make 指令

| 指令 | 說明 |
|------|------|
| `make build` | 建構 Docker 映像檔 |
| `make up` | 啟動容器（背景執行） |
| `make down` | 停止並移除容器 |
| `make shell` | 進入容器 shell |
| `make composer-install` | 安裝 Composer 依賴 |
| `make composer-update` | 更新 Composer 依賴 |
| `make test` | 執行測試 |
| `make cs` | 執行程式碼風格檢查 |
| `make cs-fix` | 自動修正程式碼風格 |
| `make php-version` | 顯示 PHP 版本 |

### 不使用 Make（直接使用 Docker Compose）

```bash
# 建構映像檔
docker-compose build

# 安裝依賴
docker-compose run --rm php composer install

# 執行測試
docker-compose run --rm php vendor/bin/phpunit

# 進入容器 shell
docker-compose run --rm php bash

# 檢查 PHP 版本
docker-compose run --rm php php -v
```

## 測試

```bash
# 執行測試
composer test

# 執行測試並產生覆蓋報告
composer test-coverage

# 程式碼風格檢查
composer cs

# 自動修復程式碼風格
composer cs-fix
```

## 測試卡號

| 類型 | 卡號 | 說明 |
|------|------|------|
| 信用卡（一次付清/分期） | 4000-2211-1111-1111 | 一般測試 |
| 紅利折抵 | 4003-5511-1111-1111 | 紅利測試 |
| 美國運通卡 | 3760-000000-00006 | AMEX 測試 |

測試卡號的有效月年及卡片背面末三碼可任意填寫。

## API 文件

本套件依據藍新金流「線上交易-幕前支付技術串接手冊」（NDNF-1.1.9）開發。

### 環境網址

| 環境 | 網址 |
|------|------|
| 測試環境 | https://ccore.newebpay.com |
| 正式環境 | https://core.newebpay.com |

### 主要 API 端點

| API | 路徑 | 說明 |
|-----|------|------|
| MPG 交易 | /MPG/mpg_gateway | 幕前支付 |
| 交易查詢 | /API/QueryTradeInfo | 查詢訂單狀態 |
| 取消授權 | /API/CreditCard/Cancel | 取消信用卡授權 |
| 請退款 | /API/CreditCard/Close | 信用卡請款/退款 |
| 電子錢包退款 | /API/EWallet/Refund | LINE Pay 等退款 |

## 目錄結構

```
newebpay/
├── src/
│   ├── Content.php                 # 基礎內容類別
│   ├── FormBuilder.php             # HTML 表單產生器
│   ├── Actions/                    # 退款/取消授權
│   ├── Contracts/                  # 介面定義
│   ├── Exceptions/                 # 例外類別
│   ├── Infrastructure/             # 加解密器
│   ├── Laravel/                    # Laravel 整合
│   ├── Notifications/              # 通知處理器
│   ├── Operations/                 # 支付操作
│   ├── Parameter/                  # 參數常數
│   └── Queries/                    # 查詢 API
├── tests/                          # 單元測試
├── config/                         # Laravel 設定檔
├── examples/                       # 範例程式
├── Dockerfile                      # Docker 映像檔配置
├── docker-compose.yml              # Docker Compose 配置
├── Makefile                        # 便捷指令
├── composer.json
├── phpunit.xml
└── README.md
```

## 授權

MIT License

## 貢獻

歡迎提交 Issue 和 Pull Request。

## 相關連結

- [藍新金流官網](https://www.newebpay.com/)
- [藍新金流商店後台](https://www.newebpay.com/main/index)
