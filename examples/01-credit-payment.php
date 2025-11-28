<?php

/**
 * 信用卡一次付清範例。
 *
 * 此範例展示如何使用藍新金流 SDK 建立信用卡付款請求。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\NewebPay\FormBuilder;
use CarlLee\NewebPay\Operations\CreditPayment;

// 設定商店資訊（請替換為您的實際資訊）
$merchantId = 'MS12345678';
$hashKey = '12345678901234567890123456789012';
$hashIV = '1234567890123456';

// 建立信用卡付款
$payment = new CreditPayment($merchantId, $hashKey, $hashIV);

$payment
    ->setTestMode(true)                                  // 測試環境
    ->setMerchantOrderNo('ORDER' . time())               // 訂單編號
    ->setAmt(1000)                                       // 金額
    ->setItemDesc('測試商品')                             // 商品描述
    ->setEmail('test@example.com')                       // 買家 Email
    ->setReturnURL('https://your-site.com/return')       // 付款完成返回網址
    ->setNotifyURL('https://your-site.com/notify')       // 付款結果通知網址
    ->setClientBackURL('https://your-site.com/back')     // 返回商店網址
    ->setRedeem(1);                                      // 啟用紅利折抵

// 方法 1：取得加密後的資料，自行建立表單
$content = $payment->getContent();
echo "MerchantID: {$content['MerchantID']}\n";
echo "TradeInfo: {$content['TradeInfo']}\n";
echo "TradeSha: {$content['TradeSha']}\n";
echo "Version: {$content['Version']}\n";
echo "API URL: {$payment->getApiUrl()}\n\n";

// 方法 2：使用 FormBuilder 產生 HTML 表單
$form = FormBuilder::create($payment)
    ->setFormId('payment-form')
    ->setAutoSubmit(true);

echo "HTML Form:\n";
echo $form->build();
