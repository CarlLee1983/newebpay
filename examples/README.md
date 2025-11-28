# 範例程式

此目錄包含藍新金流 SDK 的完整使用範例。

## 範例列表

### 支付方式

| 編號 | 檔案 | 說明 |
|------|------|------|
| 01 | `01-credit-payment.php` | 信用卡一次付清 |
| 02 | `02-atm-payment.php` | ATM 虛擬帳號轉帳 |
| 07 | `07-credit-installment.php` | 信用卡分期付款 |
| 08 | `08-cvs-payment.php` | 超商代碼繳費 |
| 09 | `09-barcode-payment.php` | 超商條碼繳費 |
| 10 | `10-linepay-payment.php` | LINE Pay |
| 11 | `11-taiwanpay-payment.php` | 台灣 Pay |
| 12 | `12-all-in-one-payment.php` | 全支付方式（多選） |
| 18 | `18-cvscom-payment.php` | 超商取貨付款 |

### 通知處理

| 編號 | 檔案 | 說明 |
|------|------|------|
| 03 | `03-notify-handler.php` | 支付完成通知處理 |
| 13 | `13-atm-notify-handler.php` | ATM 取號通知處理 |
| 14 | `14-cvs-notify-handler.php` | 超商取號通知處理 |

### 查詢

| 編號 | 檔案 | 說明 |
|------|------|------|
| 04 | `04-query-order.php` | 交易查詢 |
| 19 | `19-query-credit-detail.php` | 信用卡交易明細查詢 |

### 退款/取消

| 編號 | 檔案 | 說明 |
|------|------|------|
| 05 | `05-credit-refund.php` | 信用卡退款（簡易） |
| 15 | `15-credit-cancel.php` | 信用卡取消授權 |
| 16 | `16-credit-close.php` | 信用卡請款/退款/取消 |
| 17 | `17-ewallet-refund.php` | 電子錢包退款 |

### Laravel 整合

| 編號 | 檔案 | 說明 |
|------|------|------|
| 06 | `06-laravel-usage.php` | Laravel 整合範例 |

### 前端框架整合

| 編號 | 檔案 | 說明 |
|------|------|------|
| 20 | `20-frontend-integration.md` | Vue / React / Next.js / Nuxt 整合指南 |

## 執行前準備

1. 安裝相依套件：

```bash
cd /path/to/newebpay
composer install
```

2. 替換範例中的商店資訊：
   - `$merchantId` - 特店編號
   - `$hashKey` - HashKey
   - `$hashIV` - HashIV

## 測試環境

所有範例預設使用測試環境 (`setTestMode(true)`)。

正式上線時請改為 `setTestMode(false)` 或移除此設定。

## 測試卡號

| 類型 | 卡號 | 說明 |
|------|------|------|
| 信用卡（一次付清/分期） | 4000-2211-1111-1111 | 一般測試 |
| 紅利折抵 | 4003-5511-1111-1111 | 紅利測試 |
| 美國運通卡 | 3760-000000-00006 | AMEX 測試 |

測試卡號的有效月年及卡片背面末三碼可任意填寫。

## 金額限制

| 支付方式 | 最小金額 | 最大金額 |
|----------|----------|----------|
| 超商代碼繳費 | 30 | 20,000 |
| 超商條碼繳費 | 20 | 40,000 |
| 超商取貨付款 | 30 | 20,000 |

## 回傳網址說明

| 參數 | 說明 |
|------|------|
| `ReturnURL` | 付款完成後，前端導向網址 |
| `NotifyURL` | 付款完成後，背景通知網址（Server to Server） |
| `CustomerURL` | ATM/超商取號完成後，前端導向網址 |
| `ClientBackURL` | 消費者點選「返回商店」按鈕的導向網址 |

## 注意事項

1. **NotifyURL** 必須是可公開存取的 HTTPS 網址
2. 測試環境的通知可使用藍新後台的「模擬觸發」功能
3. 實際部署時，請確保 NotifyURL 能正確處理重複通知
4. 建議將訂單編號 (`MerchantOrderNo`) 設為唯一值
