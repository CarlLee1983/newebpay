<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Parameter;

/**
 * 金融機構類型。
 *
 * 依據藍新金流 NDNF-1.1.9 文件定義。
 */
class BankType
{
    /** 台灣銀行 */
    public const BOT = 'BOT';

    /** 華南銀行 */
    public const HNCB = 'HNCB';

    /** 彰化銀行 */
    public const CHB = 'CHB';

    /**
     * 取得所有金融機構類型。
     *
     * @return array<string>
     */
    public static function all(): array
    {
        return [
            self::BOT,
            self::HNCB,
            self::CHB,
        ];
    }

    /**
     * 取得類型描述。
     *
     * @param string $type 金融機構類型
     * @return string
     */
    public static function getDescription(string $type): string
    {
        $descriptions = [
            self::BOT => '台灣銀行',
            self::HNCB => '華南銀行',
            self::CHB => '彰化銀行',
        ];

        return $descriptions[$type] ?? '未知金融機構';
    }
}
