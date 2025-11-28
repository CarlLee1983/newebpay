<?php

/**
 * ATM 轉帳範例。
 *
 * 此範例展示如何使用藍新金流 SDK 建立 ATM 虛擬帳號付款請求。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\NewebPay\FormBuilder;
use CarlLee\NewebPay\Operations\AtmPayment;

// 設定商店資訊
$merchantId = 'MS12345678';
$hashKey = '12345678901234567890123456789012';
$hashIV = '1234567890123456';

// 建立 ATM 轉帳付款
$payment = new AtmPayment($merchantId, $hashKey, $hashIV);

$payment
    ->setTestMode(true)
    ->setMerchantOrderNo('ATM' . time())
    ->setAmt(2000)
    ->setItemDesc('ATM 轉帳測試')
    ->setEmail('test@example.com')
    ->setReturnURL('https://your-site.com/return')
    ->setNotifyURL('https://your-site.com/notify')
    ->setCustomerURL('https://your-site.com/customer')   // 取號完成返回網址
    ->setExpireDate(date('Y-m-d', strtotime('+7 days'))) // 繳費期限
    ->setBankType(AtmPayment::BANK_BOT);                 // 指定台灣銀行

// 產生表單
$form = FormBuilder::create($payment)->setAutoSubmit(false);

echo $form->build();
