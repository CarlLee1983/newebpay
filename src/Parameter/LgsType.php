<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Parameter;

/**
 * 物流類型列舉。
 *
 * 對應藍新金流 LgsType 參數。
 */
enum LgsType: string
{
    /** 全家 */
    case Family = 'FAMILY';

    /** 7-ELEVEN */
    case Seven = 'UNIMART';

    /** 萊爾富 */
    case HiLife = 'HILIFE';

    /** OK mart */
    case OkMart = 'OKMART';

    /**
     * 取得物流類型名稱。
     */
    public function name(): string
    {
        return match ($this) {
            self::Family => '全家',
            self::Seven => '7-ELEVEN',
            self::HiLife => '萊爾富',
            self::OkMart => 'OK mart',
        };
    }

    /**
     * 從字串值建立列舉。
     */
    public static function fromString(string $value): ?self
    {
        return self::tryFrom($value);
    }

    /**
     * 取得所有物流類型值。
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
