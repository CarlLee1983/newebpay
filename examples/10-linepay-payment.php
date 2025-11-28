<?php

/**
 * LINE Pay 支付範例。
 *
 * 此範例展示如何使用藍新金流 SDK 建立 LINE Pay 付款請求。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\NewebPay\FormBuilder;
use CarlLee\NewebPay\Operations\LinePayPayment;

// 設定商店資訊
$merchantId = 'MS12345678';
$hashKey = '12345678901234567890123456789012';
$hashIV = '1234567890123456';

// 建立 LINE Pay 付款
$payment = new LinePayPayment($merchantId, $hashKey, $hashIV);

$payment
    ->setTestMode(true)
    ->setMerchantOrderNo('LINE' . time())
    ->setAmt(299)
    ->setItemDesc('LINE Pay 測試商品')
    ->setEmail('test@example.com')
    ->setImageUrl('https://your-site.com/product-image.jpg')  // 商品圖片（選填）
    ->setReturnURL('https://your-site.com/return')
    ->setNotifyURL('https://your-site.com/notify')
    ->setClientBackURL('https://your-site.com/back');

// 產生表單
$form = FormBuilder::create($payment)->setAutoSubmit(true);

echo "=== LINE Pay 付款表單 ===\n\n";
echo $form->build();

