<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Contracts;

/**
 * 支付操作介面。
 */
interface PaymentInterface
{
    /**
     * 設定特店訂單編號。
     *
     * @param string $orderNo 訂單編號
     * @return self
     */
    public function setMerchantOrderNo(string $orderNo);

    /**
     * 設定訂單金額。
     *
     * @param int $amount 金額
     * @return self
     */
    public function setAmt(int $amount);

    /**
     * 設定商品資訊。
     *
     * @param string $desc 商品資訊
     * @return self
     */
    public function setItemDesc(string $desc);

    /**
     * 設定支付完成返回網址。
     *
     * @param string $url 網址
     * @return self
     */
    public function setReturnURL(string $url);

    /**
     * 設定支付通知網址。
     *
     * @param string $url 網址
     * @return self
     */
    public function setNotifyURL(string $url);

    /**
     * 取得請求路徑。
     *
     * @return string
     */
    public function getRequestPath(): string;

    /**
     * 取得 Payload。
     *
     * @return array<string, mixed>
     */
    public function getPayload(): array;

    /**
     * 取得已加密的內容。
     *
     * @return array<string, string>
     */
    public function getContent(): array;
}
