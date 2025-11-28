<?php

/**
 * 超商取號通知處理範例。
 *
 * 此範例展示如何處理超商代碼/條碼取號完成的回傳通知。
 * 當消費者選擇超商繳費後，藍新會回傳繳費代碼/條碼資訊到 CustomerURL。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\NewebPay\Exceptions\NewebPayException;
use CarlLee\NewebPay\Notifications\CvsNotify;

// 設定商店資訊
$hashKey = '12345678901234567890123456789012';
$hashIV = '1234567890123456';

// 建立超商通知處理器
$notify = new CvsNotify($hashKey, $hashIV);

// 取得 POST 資料
$postData = $_POST;

try {
    $notify->verifyOrFail($postData);

    if ($notify->isSuccess()) {
        // 取號成功
        $merchantOrderNo = $notify->getMerchantOrderNo();
        $amt = $notify->getAmt();
        $paymentType = $notify->getPaymentType();
        
        // 超商特有資訊
        $codeNo = $notify->getCodeNo();          // 繳費代碼
        $storeType = $notify->getStoreType();    // 超商類型
        $expireDate = $notify->getExpireDate();  // 繳費截止日期
        $expireTime = $notify->getExpireTime();  // 繳費截止時間

        echo "超商取號成功！\n";
        echo "訂單編號：{$merchantOrderNo}\n";
        echo "付款金額：{$amt}\n";
        echo "付款方式：{$paymentType}\n";
        echo "超商類型：{$storeType}\n";
        echo "繳費代碼：{$codeNo}\n";
        echo "繳費期限：{$expireDate} {$expireTime}\n";

        // 條碼繳費特有資訊
        if ($paymentType === 'BARCODE') {
            $barcode1 = $notify->getBarcode1();
            $barcode2 = $notify->getBarcode2();
            $barcode3 = $notify->getBarcode3();

            echo "條碼 1：{$barcode1}\n";
            echo "條碼 2：{$barcode2}\n";
            echo "條碼 3：{$barcode3}\n";
        }

        // TODO: 儲存繳費資訊
        // saveCvsInfo($merchantOrderNo, $codeNo, $expireDate);
        
        // TODO: 發送繳費通知給消費者
        // sendPaymentNotification($merchantOrderNo, $codeNo, $expireDate);
    } else {
        // 取號失敗
        $status = $notify->getStatus();
        $message = $notify->getMessage();

        echo "超商取號失敗：[{$status}] {$message}\n";
    }
} catch (NewebPayException $e) {
    // 驗證失敗
    http_response_code(400);
    echo "驗證失敗：{$e->getMessage()}\n";
    exit;
}

