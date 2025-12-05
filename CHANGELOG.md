# Changelog

本檔案記錄藍新金流 PHP SDK 的所有重要變更。

格式基於 [Keep a Changelog](https://keepachangelog.com/zh-TW/1.0.0/)，
版本號遵循 [語意化版本](https://semver.org/lang/zh-TW/)。

## [Unreleased]

## [2.1.0] - 2025-12-05

### 新增

- **測試輔助 (Testing Helpers)**：新增 `NewebPay::fake()` 方法，支援在測試中模擬支付請求。
  - 支援 `assertSent`, `assertNotSent`, `assertSentCount` 等斷言方法。
- **Laravel 事件整合**：新增 `PaymentReceived` 事件。
  - 當 `PaymentNotify` 驗證成功時，自動透過 Laravel Event Dispatcher 發送事件。
  - 新增 `LaravelPaymentNotify` 類別以處理事件發送邏輯。

## [2.0.3] - 2025-12-05

### 文件

- **README 更新**：重新設計 README.md，包含現代化排版、Badges、更清晰的範例與表格。
- **社群健康檔案**：新增 `CONTRIBUTING.md`、Issue Templates (`bug_report.md`, `feature_request.md`) 與 `PULL_REQUEST_TEMPLATE.md`。

## [2.0.2] - 2025-11-28

### 新增

- **簡化 API**：新增 `NewebPay::payment()` 方法，支援更簡潔的支付流程
  ```php
  return NewebPay::payment($orderNo, $amt, $desc, $email)->submit();
  ```
- 支援鏈式呼叫選擇支付方式：`->creditCard()`, `->atm()`, `->cvs()`, `->linePay()` 等
- 新增 `PaymentBuilder` 類別

### 文件

- 更新 README.md 加入簡化 API 說明
- 新增 Laravel 版本支援說明（Laravel 10 ~ 11）

## [2.0.1] - 2025-11-28

### 文件

- 新增前端框架整合指南（Vue 3 / React / Next.js / Nuxt 3）
- 更新 README.md 加入前端整合範例
- 更新範例檔案使用 PHP 8.1 Enum 語法

## [2.0.0] - 2025-11-28

### 變更

- **重大變更**：最低 PHP 版本需求提升至 PHP 8.3
- 更新相依套件版本以配合 PHP 8.3

### 重構（採用 PHP 8.x 新特性）

- **Enums (PHP 8.1)**：將 Parameter 類別改為 Enums
  - `PaymentType` - 支付類型列舉
  - `TradeStatus` - 交易狀態列舉
  - `LgsType` - 物流類型列舉
  - `BankType` - 金融機構類型列舉

- **Constructor Property Promotion (PHP 8.0)**：簡化所有類別的建構子

- **Readonly Classes (PHP 8.2)**：將 Infrastructure 類別改為 readonly class
  - `AES256Encoder`
  - `CheckValueEncoder`

- **Typed Class Constants (PHP 8.3)**：為常數加上型別宣告

- **#[\Override] Attribute (PHP 8.3)**：在子類別覆寫方法時使用

- **Match Expression (PHP 8.0)**：取代 switch 和 array map

- **Union Types (PHP 8.0)**：使用聯合型別宣告

### 注意

若需要 PHP 7.4+ 支援，請使用 1.x 分支版本：
```bash
composer require carllee1983/newebpay:^1.0
```

## [1.0.0] - 2025-11-28

### 新增

- 核心基礎設施
  - AES-256-CBC 加解密器 (`AES256Encoder`)
  - CheckValue (TradeSha) 驗證器 (`CheckValueEncoder`)
  - 例外類別 (`NewebPayException`)
  - HTML Form 產生器 (`FormBuilder`)

- MPG 支付操作
  - 信用卡一次付清 (`CreditPayment`)
  - 信用卡分期 (`CreditInstallment`)
  - WebATM (`WebAtmPayment`)
  - ATM 轉帳 (`AtmPayment`)
  - 超商代碼繳費 (`CvsPayment`)
  - 超商條碼繳費 (`BarcodePayment`)
  - LINE Pay (`LinePayPayment`)
  - 台灣 Pay (`TaiwanPayPayment`)
  - 玉山 Wallet (`EsunWalletPayment`)
  - BitoPay (`BitoPayPayment`)
  - 超商取貨付款 (`CvscomPayment`)
  - 全支付方式 (`AllInOnePayment`)

- Webhook 通知處理
  - 支付完成通知 (`PaymentNotify`)
  - ATM 取號通知 (`AtmNotify`)
  - 超商取號通知 (`CvsNotify`)

- 交易查詢
  - 交易查詢 (`QueryOrder`)
  - 信用卡明細查詢 (`QueryCreditDetail`)

- 退款/取消授權
  - 信用卡取消授權 (`CreditCancel`)
  - 信用卡請退款 (`CreditClose`)
  - 電子錢包退款 (`EWalletRefund`)

- Laravel 整合
  - Service Provider (`NewebPayServiceProvider`)
  - Facades (`NewebPay`, `NewebPayNotify`)
  - 設定檔 (`config/newebpay.php`)

- 參數類別
  - 支付類型 (`PaymentType`)
  - 交易狀態 (`TradeStatus`)
  - 物流類型 (`LgsType`)

- 單元測試 (149 tests, 312 assertions)

[Unreleased]: https://github.com/CarlLee1983/newebpay/compare/v2.0.2...HEAD
[2.0.2]: https://github.com/CarlLee1983/newebpay/compare/v2.0.1...v2.0.2
[2.0.1]: https://github.com/CarlLee1983/newebpay/compare/v2.0.0...v2.0.1
[2.0.0]: https://github.com/CarlLee1983/newebpay/compare/v1.0.0...v2.0.0
[1.0.0]: https://github.com/CarlLee1983/newebpay/releases/tag/v1.0.0

