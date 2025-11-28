<?php

/**
 * 超商取貨付款範例。
 *
 * 此範例展示如何使用藍新金流 SDK 建立超商取貨付款（物流）請求。
 * 金額限制：30~20,000 元。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\NewebPay\FormBuilder;
use CarlLee\NewebPay\Operations\CvscomPayment;
use CarlLee\NewebPay\Parameter\LgsType;

// 設定商店資訊
$merchantId = 'MS12345678';
$hashKey = '12345678901234567890123456789012';
$hashIV = '1234567890123456';

// 建立超商取貨付款
$payment = new CvscomPayment($merchantId, $hashKey, $hashIV);

$payment
    ->setTestMode(true)
    ->setMerchantOrderNo('CVSCOM' . time())
    ->setAmt(399)                                        // 金額 30~20,000 元
    ->setItemDesc('超商取貨付款測試')
    ->setEmail('test@example.com')
    ->setLgsType(LgsType::Seven)                         // 指定 7-ELEVEN
    ->setReturnURL('https://your-site.com/return')
    ->setNotifyURL('https://your-site.com/notify')
    ->setClientBackURL('https://your-site.com/back');

/*
 * 支援的物流類型（PHP 8.1 Enum）：
 * - LgsType::Family  全家
 * - LgsType::Seven   7-ELEVEN (UNIMART)
 * - LgsType::HiLife  萊爾富
 * - LgsType::OkMart  OK mart
 */

// 產生表單
$form = FormBuilder::create($payment)->setAutoSubmit(true);

echo "=== 超商取貨付款表單 ===\n\n";
echo $form->build();
