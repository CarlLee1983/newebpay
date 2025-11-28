<?php

/**
 * 信用卡交易明細查詢範例。
 *
 * 此範例展示如何查詢信用卡交易的詳細資訊。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\NewebPay\Exceptions\NewebPayException;
use CarlLee\NewebPay\Queries\QueryCreditDetail;

// 設定商店資訊
$merchantId = 'MS12345678';
$hashKey = '12345678901234567890123456789012';
$hashIV = '1234567890123456';

// 建立信用卡明細查詢物件
$query = QueryCreditDetail::create($merchantId, $hashKey, $hashIV)
    ->setTestMode(true);

// ===== 方式一：使用特店訂單編號查詢 =====
echo "=== 使用訂單編號查詢 ===\n";
try {
    $result = $query->queryByOrderNo('ORDER1234567890', 1000);

    echo "查詢成功！\n";
    echo "訂單編號：{$result['MerchantOrderNo']}\n";
    echo "藍新交易序號：{$result['TradeNo']}\n";
    echo "交易金額：{$result['Amt']}\n";
    echo "授權碼：{$result['Auth']}\n";
    echo "卡號末四碼：{$result['Card4No']}\n";

    // 請款狀態
    if (isset($result['CloseStatus'])) {
        echo "請款狀態：{$result['CloseStatus']}\n";
        echo "請款金額：{$result['CloseAmt']}\n";
    }

    // 退款狀態
    if (isset($result['BackStatus'])) {
        echo "退款狀態：{$result['BackStatus']}\n";
        echo "退款金額：{$result['BackBalance']}\n";
    }
} catch (NewebPayException $e) {
    echo "查詢失敗：{$e->getMessage()}\n";
}

echo "\n";

// ===== 方式二：使用藍新交易序號查詢 =====
echo "=== 使用藍新交易序號查詢 ===\n";
try {
    $result = $query->queryByTradeNo('20231231000001', 1000);

    echo "查詢成功！\n";
    echo "訂單編號：{$result['MerchantOrderNo']}\n";
    echo "交易金額：{$result['Amt']}\n";
} catch (NewebPayException $e) {
    echo "查詢失敗：{$e->getMessage()}\n";
}
