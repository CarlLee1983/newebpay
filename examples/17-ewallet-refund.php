<?php

/**
 * 電子錢包退款範例。
 *
 * 此範例展示如何對電子錢包交易（LINE Pay、玉山 Wallet、台灣 Pay 等）進行退款。
 * 
 * 注意：電子錢包退款有時間限制，請參考藍新金流文件。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\NewebPay\Actions\EWalletRefund;
use CarlLee\NewebPay\Exceptions\NewebPayException;
use CarlLee\NewebPay\Parameter\PaymentType;

// 設定商店資訊
$merchantId = 'MS12345678';
$hashKey = '12345678901234567890123456789012';
$hashIV = '1234567890123456';

// 建立電子錢包退款物件
$refund = EWalletRefund::create($merchantId, $hashKey, $hashIV)
    ->setTestMode(true);

// 訂單資訊
$merchantOrderNo = 'LINE1234567890';
$refundAmt = 299;

// ===== LINE Pay 退款 =====
echo "=== LINE Pay 退款範例 ===\n";
try {
    $result = $refund->refund($merchantOrderNo, $refundAmt, PaymentType::LINEPAY);

    echo "退款成功！\n";
    echo "訂單編號：{$result['MerchantOrderNo']}\n";
    echo "退款金額：{$result['Amt']}\n";
} catch (NewebPayException $e) {
    echo "退款失敗：{$e->getMessage()}\n";
}

echo "\n";

// ===== 玉山 Wallet 退款 =====
echo "=== 玉山 Wallet 退款範例 ===\n";
try {
    $result = $refund->refund('ESUN1234567890', 500, PaymentType::ESUNWALLET);

    echo "退款成功！\n";
    echo "訂單編號：{$result['MerchantOrderNo']}\n";
} catch (NewebPayException $e) {
    echo "退款失敗：{$e->getMessage()}\n";
}

echo "\n";

// ===== 台灣 Pay 退款 =====
echo "=== 台灣 Pay 退款範例 ===\n";
try {
    $result = $refund->refund('TWPAY1234567890', 1000, PaymentType::TAIWANPAY);

    echo "退款成功！\n";
    echo "訂單編號：{$result['MerchantOrderNo']}\n";
} catch (NewebPayException $e) {
    echo "退款失敗：{$e->getMessage()}\n";
}

/*
 * 支援的電子錢包類型：
 * - PaymentType::LINEPAY      LINE Pay
 * - PaymentType::ESUNWALLET   玉山 Wallet
 * - PaymentType::TAIWANPAY    台灣 Pay
 * - PaymentType::BITOPAY      BitoPay
 * - PaymentType::EZPWECHAT    簡單付微信
 * - PaymentType::EZPALIPAY    簡單付支付寶
 */

