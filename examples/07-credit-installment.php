<?php

/**
 * 信用卡分期付款範例。
 *
 * 此範例展示如何使用藍新金流 SDK 建立信用卡分期付款請求。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\NewebPay\FormBuilder;
use CarlLee\NewebPay\Operations\CreditInstallment;

// 設定商店資訊（請替換為您的實際資訊）
$merchantId = 'MS12345678';
$hashKey = '12345678901234567890123456789012';
$hashIV = '1234567890123456';

// 建立信用卡分期付款
$payment = new CreditInstallment($merchantId, $hashKey, $hashIV);

$payment
    ->setTestMode(true)                                  // 測試環境
    ->setMerchantOrderNo('INST' . time())                // 訂單編號
    ->setAmt(6000)                                       // 金額（建議較高金額才適合分期）
    ->setItemDesc('分期商品測試')                         // 商品描述
    ->setEmail('test@example.com')                       // 買家 Email
    ->setInstallment([3, 6, 12])                         // 提供 3/6/12 期選項
    ->setRedeem(0)                                       // 不啟用紅利折抵
    ->setReturnURL('https://your-site.com/return')       // 付款完成返回網址
    ->setNotifyURL('https://your-site.com/notify')       // 付款結果通知網址
    ->setClientBackURL('https://your-site.com/back');    // 返回商店網址

// 也可以只提供單一期數
// $payment->setInstallment(6);  // 只提供 6 期

// 產生表單
$form = FormBuilder::create($payment)
    ->setFormId('installment-form')
    ->setAutoSubmit(true);

echo "=== 信用卡分期付款表單 ===\n\n";
echo $form->build();
