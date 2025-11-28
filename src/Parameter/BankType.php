<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Parameter;

/**
 * 金融機構類型列舉。
 *
 * 依據藍新金流 NDNF-1.1.9 文件定義。
 */
enum BankType: string
{
    /** 台灣銀行 */
    case Bot = 'BOT';

    /** 華南銀行 */
    case Hncb = 'HNCB';

    /** 彰化銀行 */
    case Chb = 'CHB';

    /**
     * 取得類型描述。
     */
    public function description(): string
    {
        return match ($this) {
            self::Bot => '台灣銀行',
            self::Hncb => '華南銀行',
            self::Chb => '彰化銀行',
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
     * 取得所有金融機構類型值。
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
