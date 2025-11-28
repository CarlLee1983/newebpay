<?php

/**
 * Laravel 整合範例。
 *
 * 此範例展示如何在 Laravel 中使用藍新金流 SDK。
 */

// 注意：此檔案僅供參考，請在實際 Laravel 專案中使用

/*
 * 1. 安裝套件
 *
 * composer require carllee1983/newebpay
 *
 * 2. 發布設定檔
 *
 * php artisan vendor:publish --tag=newebpay-config
 *
 * 3. 設定環境變數（.env）
 *
 * NEWEBPAY_TEST_MODE=true
 * NEWEBPAY_MERCHANT_ID=MS12345678
 * NEWEBPAY_HASH_KEY=12345678901234567890123456789012
 * NEWEBPAY_HASH_IV=1234567890123456
 * NEWEBPAY_RETURN_URL=https://your-site.com/payment/return
 * NEWEBPAY_NOTIFY_URL=https://your-site.com/payment/notify
 */

// ==========================================
// 控制器範例
// ==========================================

namespace App\Http\Controllers;

use CarlLee\NewebPay\Laravel\Facades\NewebPay;
use CarlLee\NewebPay\Laravel\Facades\NewebPayNotify;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * 建立信用卡付款。
     */
    public function createCreditPayment(Request $request)
    {
        $payment = NewebPay::credit()
            ->setMerchantOrderNo('ORDER' . time())
            ->setAmt($request->input('amount'))
            ->setItemDesc($request->input('item_desc'))
            ->setEmail($request->input('email'))
            ->setReturnURL(config('newebpay.urls.return_url'))
            ->setNotifyURL(config('newebpay.urls.notify_url'));

        // 使用表單產生器
        $form = NewebPay::form($payment)->setAutoSubmit(true);

        return view('payment.checkout', [
            'form' => $form->build(),
        ]);
    }

    /**
     * 建立 ATM 轉帳付款。
     */
    public function createAtmPayment(Request $request)
    {
        $payment = NewebPay::atm()
            ->setMerchantOrderNo('ATM' . time())
            ->setAmt($request->input('amount'))
            ->setItemDesc($request->input('item_desc'))
            ->setExpireDate(now()->addDays(7)->format('Y-m-d'))
            ->setReturnURL(config('newebpay.urls.return_url'))
            ->setNotifyURL(config('newebpay.urls.notify_url'))
            ->setCustomerURL(config('newebpay.urls.customer_url'));

        $form = NewebPay::form($payment)->setAutoSubmit(true);

        return view('payment.checkout', [
            'form' => $form->build(),
        ]);
    }

    /**
     * 處理付款通知（NotifyURL）。
     */
    public function notify(Request $request)
    {
        try {
            NewebPayNotify::verifyOrFail($request->all());

            if (NewebPayNotify::isSuccess()) {
                $merchantOrderNo = NewebPayNotify::getMerchantOrderNo();
                $amt = NewebPayNotify::getAmt();

                // 更新訂單狀態
                // Order::where('order_no', $merchantOrderNo)
                //     ->update(['status' => 'paid', 'paid_at' => now()]);

                return response('OK');
            }

            // 付款失敗
            return response('FAIL');
        } catch (\Exception $e) {
            return response('ERROR', 400);
        }
    }

    /**
     * 處理付款返回（ReturnURL）。
     */
    public function return(Request $request)
    {
        if (NewebPayNotify::verify($request->all()) && NewebPayNotify::isSuccess()) {
            return redirect()->route('orders.show', NewebPayNotify::getMerchantOrderNo())
                ->with('success', '付款成功！');
        }

        return redirect()->route('orders.index')
            ->with('error', '付款失敗，請重試。');
    }

    /**
     * 查詢訂單。
     */
    public function queryOrder(string $orderNo, int $amt)
    {
        try {
            $result = NewebPay::queryOrder()->query($orderNo, $amt);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * 執行退款。
     */
    public function refund(Request $request)
    {
        try {
            $result = NewebPay::creditClose()->refund(
                $request->input('order_no'),
                $request->input('amount')
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
