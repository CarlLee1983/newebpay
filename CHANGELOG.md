# Changelog

本檔案記錄藍新金流 PHP SDK 的所有重要變更。

格式基於 [Keep a Changelog](https://keepachangelog.com/zh-TW/1.0.0/)，
版本號遵循 [語意化版本](https://semver.org/lang/zh-TW/)。

## [Unreleased]

## [1.0.1] - 2025-11-28

### 文件

- 新增前端框架整合指南（Vue 2/3 / React / Next.js / Nuxt 2）
- 更新 README.md 加入前端整合範例

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

[Unreleased]: https://github.com/CarlLee1983/newebpay/compare/v1.0.1...HEAD
[1.0.1]: https://github.com/CarlLee1983/newebpay/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/CarlLee1983/newebpay/releases/tag/v1.0.0

