<?php

/**
 * 信用卡取消授權範例。
 *
 * 此範例展示如何對尚未請款的信用卡交易取消授權。
 * 注意：只能取消尚未請款的交易，已請款的交易需使用退款。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\NewebPay\Actions\CreditCancel;
use CarlLee\NewebPay\Exceptions\NewebPayException;

// 設定商店資訊
$merchantId = 'MS12345678';
$hashKey = '12345678901234567890123456789012';
$hashIV = '1234567890123456';

// 建立取消授權物件
$creditCancel = CreditCancel::create($merchantId, $hashKey, $hashIV)
    ->setTestMode(true);

// 要取消的訂單資訊
$merchantOrderNo = 'ORDER1234567890';
$amt = 1000;

try {
    // 方式一：使用特店訂單編號取消（預設）
    $result = $creditCancel->cancel($merchantOrderNo, $amt);

    // 方式二：使用藍新交易序號取消
    // $tradeNo = '20231231000001';
    // $result = $creditCancel->cancel($merchantOrderNo, $amt, '1', $tradeNo);

    echo "取消授權成功！\n";
    echo "訂單編號：{$result['MerchantOrderNo']}\n";
    echo "藍新交易序號：{$result['TradeNo']}\n";
    echo "取消金額：{$result['Amt']}\n";
} catch (NewebPayException $e) {
    echo "取消授權失敗：{$e->getMessage()}\n";
}
