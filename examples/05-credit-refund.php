<?php

/**
 * 信用卡退款範例。
 *
 * 此範例展示如何對信用卡交易進行退款操作。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\NewebPay\Actions\CreditClose;
use CarlLee\NewebPay\Exceptions\NewebPayException;

// 設定商店資訊
$merchantId = 'MS12345678';
$hashKey = '12345678901234567890123456789012';
$hashIV = '1234567890123456';

// 建立請退款物件
$creditClose = CreditClose::create($merchantId, $hashKey, $hashIV)
    ->setTestMode(true);

try {
    // 執行退款
    $result = $creditClose->refund(
        'ORDER1234567890',  // 訂單編號
        500                 // 退款金額（可部分退款）
    );

    echo "退款成功！\n";
    echo "訂單編號：{$result['MerchantOrderNo']}\n";
    echo "藍新交易序號：{$result['TradeNo']}\n";
    echo "退款金額：{$result['Amt']}\n";
} catch (NewebPayException $e) {
    echo "退款失敗：{$e->getMessage()}\n";
}

