<?php

/**
 * 台灣 Pay 支付範例。
 *
 * 此範例展示如何使用藍新金流 SDK 建立台灣 Pay 付款請求。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\NewebPay\FormBuilder;
use CarlLee\NewebPay\Operations\TaiwanPayPayment;

// 設定商店資訊
$merchantId = 'MS12345678';
$hashKey = '12345678901234567890123456789012';
$hashIV = '1234567890123456';

// 建立台灣 Pay 付款
$payment = new TaiwanPayPayment($merchantId, $hashKey, $hashIV);

$payment
    ->setTestMode(true)
    ->setMerchantOrderNo('TWPAY' . time())
    ->setAmt(500)
    ->setItemDesc('台灣 Pay 測試商品')
    ->setEmail('test@example.com')
    ->setReturnURL('https://your-site.com/return')
    ->setNotifyURL('https://your-site.com/notify')
    ->setClientBackURL('https://your-site.com/back');

// 產生表單
$form = FormBuilder::create($payment)->setAutoSubmit(true);

echo "=== 台灣 Pay 付款表單 ===\n\n";
echo $form->build();

