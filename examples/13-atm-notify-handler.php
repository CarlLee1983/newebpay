<?php

/**
 * ATM 取號通知處理範例。
 *
 * 此範例展示如何處理 ATM 虛擬帳號取號完成的回傳通知。
 * 當消費者選擇 ATM 轉帳後，藍新會回傳虛擬帳號資訊到 CustomerURL。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\NewebPay\Exceptions\NewebPayException;
use CarlLee\NewebPay\Notifications\AtmNotify;

// 設定商店資訊
$hashKey = '12345678901234567890123456789012';
$hashIV = '1234567890123456';

// 建立 ATM 通知處理器
$notify = new AtmNotify($hashKey, $hashIV);

// 取得 POST 資料
$postData = $_POST;

try {
    $notify->verifyOrFail($postData);

    if ($notify->isSuccess()) {
        // 取號成功
        $merchantOrderNo = $notify->getMerchantOrderNo();
        $tradeNo = $notify->getTradeNo();
        $amt = $notify->getAmt();
        
        // ATM 特有資訊
        $bankCode = $notify->getBankCode();      // 銀行代碼
        $codeNo = $notify->getCodeNo();          // 虛擬帳號
        $expireDate = $notify->getExpireDate();  // 繳費截止日期
        $expireTime = $notify->getExpireTime();  // 繳費截止時間

        echo "ATM 取號成功！\n";
        echo "訂單編號：{$merchantOrderNo}\n";
        echo "藍新交易序號：{$tradeNo}\n";
        echo "付款金額：{$amt}\n";
        echo "銀行代碼：{$bankCode}\n";
        echo "虛擬帳號：{$codeNo}\n";
        echo "繳費期限：{$expireDate} {$expireTime}\n";

        // TODO: 儲存虛擬帳號資訊
        // saveAtmInfo($merchantOrderNo, $bankCode, $codeNo, $expireDate);
        
        // TODO: 發送繳費通知給消費者
        // sendPaymentNotification($merchantOrderNo, $bankCode, $codeNo, $expireDate);
    } else {
        // 取號失敗
        $status = $notify->getStatus();
        $message = $notify->getMessage();

        echo "ATM 取號失敗：[{$status}] {$message}\n";
    }
} catch (NewebPayException $e) {
    // 驗證失敗
    http_response_code(400);
    echo "驗證失敗：{$e->getMessage()}\n";
    exit;
}

