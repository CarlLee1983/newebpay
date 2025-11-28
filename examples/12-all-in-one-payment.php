<?php

/**
 * 全支付方式範例。
 *
 * 此範例展示如何使用 AllInOnePayment 自訂啟用多種支付方式。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\NewebPay\FormBuilder;
use CarlLee\NewebPay\Operations\AllInOnePayment;

// 設定商店資訊
$merchantId = 'MS12345678';
$hashKey = '12345678901234567890123456789012';
$hashIV = '1234567890123456';

// 建立全支付方式
$payment = new AllInOnePayment($merchantId, $hashKey, $hashIV);

$payment
    ->setTestMode(true)
    ->setMerchantOrderNo('ALL' . time())
    ->setAmt(1000)
    ->setItemDesc('多元支付測試')
    ->setEmail('test@example.com')
    ->setReturnURL('https://your-site.com/return')
    ->setNotifyURL('https://your-site.com/notify')
    ->setClientBackURL('https://your-site.com/back');

// 方式一：啟用所有支付方式
// $payment->enableAll();

// 方式二：自訂啟用特定支付方式
$payment
    ->enableCredit()           // 信用卡
    ->enableInstallment([3, 6, 12])  // 信用卡分期
    ->enableWebAtm()           // WebATM
    ->enableAtm()              // ATM 轉帳
    ->enableLinePay()          // LINE Pay
    ->enableTaiwanPay();       // 台灣 Pay

// 注意：超商類金額有限制
// ->enableCvs()              // 超商代碼 (30~20,000)
// ->enableBarcode()          // 超商條碼 (20~40,000)

// 產生表單
$form = FormBuilder::create($payment)->setAutoSubmit(true);

echo "=== 多元支付表單 ===\n\n";
echo $form->build();
