<?php

/**
 * 超商代碼繳費範例。
 *
 * 此範例展示如何使用藍新金流 SDK 建立超商代碼繳費請求。
 * 金額限制：30~20,000 元。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\NewebPay\FormBuilder;
use CarlLee\NewebPay\Operations\CvsPayment;

// 設定商店資訊
$merchantId = 'MS12345678';
$hashKey = '12345678901234567890123456789012';
$hashIV = '1234567890123456';

// 建立超商代碼繳費
$payment = new CvsPayment($merchantId, $hashKey, $hashIV);

$payment
    ->setTestMode(true)
    ->setMerchantOrderNo('CVS' . time())
    ->setAmt(500)                                        // 金額 30~20,000 元
    ->setItemDesc('超商代碼繳費測試')
    ->setEmail('test@example.com')
    ->setExpireDate(date('Y-m-d', strtotime('+7 days'))) // 繳費期限
    ->setReturnURL('https://your-site.com/return')
    ->setNotifyURL('https://your-site.com/notify')
    ->setCustomerURL('https://your-site.com/customer');  // 取號完成返回網址

// 產生表單
$form = FormBuilder::create($payment)->setAutoSubmit(true);

echo "=== 超商代碼繳費表單 ===\n\n";
echo $form->build();

