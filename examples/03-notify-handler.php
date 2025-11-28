<?php

/**
 * 支付結果通知處理範例。
 *
 * 此範例展示如何處理藍新金流回傳的付款結果通知。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\NewebPay\Exceptions\NewebPayException;
use CarlLee\NewebPay\Notifications\PaymentNotify;

// 設定商店資訊
$hashKey = '12345678901234567890123456789012';
$hashIV = '1234567890123456';

// 建立通知處理器
$notify = new PaymentNotify($hashKey, $hashIV);

// 取得 POST 資料
$postData = $_POST;

// 驗證通知資料
try {
    $notify->verifyOrFail($postData);

    // 驗證成功，處理付款結果
    if ($notify->isSuccess()) {
        // 付款成功
        $merchantOrderNo = $notify->getMerchantOrderNo();
        $tradeNo = $notify->getTradeNo();
        $amt = $notify->getAmt();
        $paymentType = $notify->getPaymentType();
        $payTime = $notify->getPayTime();

        echo "付款成功！\n";
        echo "訂單編號：{$merchantOrderNo}\n";
        echo "藍新交易序號：{$tradeNo}\n";
        echo "付款金額：{$amt}\n";
        echo "付款方式：{$paymentType}\n";
        echo "付款時間：{$payTime}\n";

        // 信用卡相關資訊
        if ($paymentType === 'CREDIT') {
            echo "授權碼：{$notify->getAuthCode()}\n";
            echo "卡號末四碼：{$notify->getCard4No()}\n";

            // 分期資訊
            if ($notify->getInst() > 0) {
                echo "分期期數：{$notify->getInst()}\n";
                echo "首期金額：{$notify->getInstFirst()}\n";
                echo "每期金額：{$notify->getInstEach()}\n";
            }
        }

        // TODO: 更新訂單狀態
        // updateOrderStatus($merchantOrderNo, 'paid');
    } else {
        // 付款失敗
        $status = $notify->getStatus();
        $message = $notify->getMessage();

        echo "付款失敗：[{$status}] {$message}\n";

        // TODO: 更新訂單狀態
        // updateOrderStatus($merchantOrderNo, 'failed');
    }
} catch (NewebPayException $e) {
    // 驗證失敗
    http_response_code(400);
    echo "驗證失敗：{$e->getMessage()}\n";
    exit;
}

