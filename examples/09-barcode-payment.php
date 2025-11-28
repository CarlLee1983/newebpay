<?php

/**
 * 超商條碼繳費範例。
 *
 * 此範例展示如何使用藍新金流 SDK 建立超商條碼繳費請求。
 * 金額限制：20~40,000 元。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\NewebPay\FormBuilder;
use CarlLee\NewebPay\Operations\BarcodePayment;

// 設定商店資訊
$merchantId = 'MS12345678';
$hashKey = '12345678901234567890123456789012';
$hashIV = '1234567890123456';

// 建立超商條碼繳費
$payment = new BarcodePayment($merchantId, $hashKey, $hashIV);

$payment
    ->setTestMode(true)
    ->setMerchantOrderNo('BAR' . time())
    ->setAmt(1000)                                       // 金額 20~40,000 元
    ->setItemDesc('超商條碼繳費測試')
    ->setEmail('test@example.com')
    ->setExpireDate(date('Y-m-d', strtotime('+7 days'))) // 繳費期限
    ->setReturnURL('https://your-site.com/return')
    ->setNotifyURL('https://your-site.com/notify')
    ->setCustomerURL('https://your-site.com/customer');  // 取號完成返回網址

// 產生表單
$form = FormBuilder::create($payment)->setAutoSubmit(true);

echo "=== 超商條碼繳費表單 ===\n\n";
echo $form->build();

