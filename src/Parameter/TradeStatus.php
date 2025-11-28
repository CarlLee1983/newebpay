<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Parameter;

/**
 * 交易狀態列舉。
 *
 * 對應藍新金流 TradeStatus 回傳參數。
 */
class TradeStatus
{
    /** 交易成功 */
    public const SUCCESS = 1;

    /** 交易付款失敗 */
    public const FAILED = 0;

    /** 交易等待付款中 */
    public const PENDING = 2;

    /** 交易已取消 */
    public const CANCELLED = 3;

    /** 交易處理中 */
    public const PROCESSING = 6;

    /**
     * 檢查交易是否成功。
     *
     * @param int|string $status 狀態值
     * @return bool
     */
    public static function isSuccess($status): bool
    {
        return (int) $status === self::SUCCESS;
    }

    /**
     * 檢查交易是否等待中。
     *
     * @param int|string $status 狀態值
     * @return bool
     */
    public static function isPending($status): bool
    {
        return (int) $status === self::PENDING;
    }

    /**
     * 檢查交易是否失敗。
     *
     * @param int|string $status 狀態值
     * @return bool
     */
    public static function isFailed($status): bool
    {
        return (int) $status === self::FAILED;
    }

    /**
     * 取得狀態說明。
     *
     * @param int|string $status 狀態值
     * @return string
     */
    public static function getDescription($status): string
    {
        $descriptions = [
            self::SUCCESS => '交易成功',
            self::FAILED => '交易付款失敗',
            self::PENDING => '交易等待付款中',
            self::CANCELLED => '交易已取消',
            self::PROCESSING => '交易處理中',
        ];

        return $descriptions[(int) $status] ?? '未知狀態';
    }
}
