<?php

/**
 * 信用卡請退款範例。
 *
 * 此範例展示如何對信用卡交易進行請款或退款操作。
 *
 * 交易流程：
 * 1. 授權 -> 請款 -> 退款
 * 2. 授權 -> 取消授權（尚未請款時）
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

// 訂單資訊
$merchantOrderNo = 'ORDER1234567890';

// ===== 範例 1：請款 =====
// 授權成功後，需要請款才會實際扣款
echo "=== 請款範例 ===\n";
try {
    $result = $creditClose->pay($merchantOrderNo, 1000);

    echo "請款成功！\n";
    echo "訂單編號：{$result['MerchantOrderNo']}\n";
    echo "請款金額：{$result['Amt']}\n";
} catch (NewebPayException $e) {
    echo "請款失敗：{$e->getMessage()}\n";
}

echo "\n";

// ===== 範例 2：退款 =====
// 請款後可以退款（可部分退款）
echo "=== 退款範例 ===\n";
try {
    $result = $creditClose->refund($merchantOrderNo, 500);  // 退款 500 元

    echo "退款成功！\n";
    echo "訂單編號：{$result['MerchantOrderNo']}\n";
    echo "退款金額：{$result['Amt']}\n";
} catch (NewebPayException $e) {
    echo "退款失敗：{$e->getMessage()}\n";
}

echo "\n";

// ===== 範例 3：取消請款/退款 =====
// 如果請款或退款尚未完成，可以取消
echo "=== 取消請退款範例 ===\n";
try {
    // 取消請款
    $result = $creditClose->cancelClose(
        $merchantOrderNo,
        1000,
        CreditClose::CLOSE_TYPE_PAY  // 1=請款, 2=退款
    );

    echo "取消請款成功！\n";
    echo "訂單編號：{$result['MerchantOrderNo']}\n";
} catch (NewebPayException $e) {
    echo "取消請款失敗：{$e->getMessage()}\n";
}
