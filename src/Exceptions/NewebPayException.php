<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Exceptions;

use Exception;

/**
 * 藍新金流例外類別。
 */
class NewebPayException extends Exception
{
    /**
     * 建立必填欄位例外。
     *
     * @param string $field 欄位名稱
     * @return static
     */
    public static function required(string $field): self
    {
        return new static("欄位 [{$field}] 為必填");
    }

    /**
     * 建立欄位過長例外。
     *
     * @param string $field 欄位名稱
     * @param int $maxLength 最大長度
     * @return static
     */
    public static function tooLong(string $field, int $maxLength): self
    {
        return new static("欄位 [{$field}] 超過最大長度 {$maxLength}");
    }

    /**
     * 建立無效值例外。
     *
     * @param string $field 欄位名稱
     * @param string $reason 原因
     * @return static
     */
    public static function invalid(string $field, string $reason = ''): self
    {
        $message = "欄位 [{$field}] 值無效";
        if ($reason) {
            $message .= "：{$reason}";
        }

        return new static($message);
    }

    /**
     * 建立解密失敗例外。
     *
     * @return static
     */
    public static function decryptFailed(): self
    {
        return new static('TradeInfo 解密失敗');
    }

    /**
     * 建立 CheckValue 驗證失敗例外。
     *
     * @return static
     */
    public static function checkValueFailed(): self
    {
        return new static('TradeSha 驗證失敗');
    }

    /**
     * 建立 API 請求失敗例外。
     *
     * @param string $message 錯誤訊息
     * @param string $code 錯誤代碼
     * @return static
     */
    public static function apiError(string $message, string $code = ''): self
    {
        $msg = $code ? "[{$code}] {$message}" : $message;

        return new static($msg);
    }
}
