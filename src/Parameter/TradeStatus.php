<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Parameter;

/**
 * 交易狀態列舉。
 *
 * 對應藍新金流 TradeStatus 回傳參數。
 */
enum TradeStatus: int
{
    /** 交易成功 */
    case Success = 1;

    /** 交易付款失敗 */
    case Failed = 0;

    /** 交易等待付款中 */
    case Pending = 2;

    /** 交易已取消 */
    case Cancelled = 3;

    /** 交易處理中 */
    case Processing = 6;

    /**
     * 檢查交易是否成功。
     */
    public function isSuccess(): bool
    {
        return $this === self::Success;
    }

    /**
     * 檢查交易是否等待中。
     */
    public function isPending(): bool
    {
        return $this === self::Pending;
    }

    /**
     * 檢查交易是否失敗。
     */
    public function isFailed(): bool
    {
        return $this === self::Failed;
    }

    /**
     * 取得狀態說明。
     */
    public function description(): string
    {
        return match ($this) {
            self::Success => '交易成功',
            self::Failed => '交易付款失敗',
            self::Pending => '交易等待付款中',
            self::Cancelled => '交易已取消',
            self::Processing => '交易處理中',
        };
    }

    /**
     * 從整數或字串值建立列舉。
     */
    public static function fromValue(int|string $value): ?self
    {
        return self::tryFrom((int) $value);
    }

    /**
     * 取得所有狀態值。
     *
     * @return array<int>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
