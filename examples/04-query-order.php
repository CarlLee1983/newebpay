<?php

/**
 * 交易查詢範例。
 *
 * 此範例展示如何查詢藍新金流的交易訂單狀態。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\NewebPay\Exceptions\NewebPayException;
use CarlLee\NewebPay\Queries\QueryOrder;

// 設定商店資訊
$merchantId = 'MS12345678';
$hashKey = '12345678901234567890123456789012';
$hashIV = '1234567890123456';

// 建立查詢物件
$query = QueryOrder::create($merchantId, $hashKey, $hashIV)
    ->setTestMode(true);

try {
    // 查詢訂單
    $result = $query->query('ORDER1234567890', 1000);

    echo "查詢成功！\n";
    echo "訂單編號：{$result['MerchantOrderNo']}\n";
    echo "藍新交易序號：{$result['TradeNo']}\n";
    echo "交易狀態：{$result['TradeStatus']}\n";
    echo "付款方式：{$result['PaymentType']}\n";
    echo "交易金額：{$result['Amt']}\n";

    // 信用卡交易詳細資訊
    if (isset($result['Auth'])) {
        echo "授權碼：{$result['Auth']}\n";
    }
    if (isset($result['CloseStatus'])) {
        echo "請款狀態：{$result['CloseStatus']}\n";
    }
    if (isset($result['BackStatus'])) {
        echo "退款狀態：{$result['BackStatus']}\n";
    }
} catch (NewebPayException $e) {
    echo "查詢失敗：{$e->getMessage()}\n";
}
