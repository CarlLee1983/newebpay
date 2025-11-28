<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Parameter;

/**
 * 物流類型列舉。
 *
 * 對應藍新金流 LgsType 參數。
 */
class LgsType
{
    /** 全家 */
    public const FAMILY = 'FAMILY';

    /** 7-ELEVEN */
    public const SEVEN = 'UNIMART';

    /** 萊爾富 */
    public const HILIFE = 'HILIFE';

    /** OK mart */
    public const OKMART = 'OKMART';

    /**
     * 取得所有物流類型。
     *
     * @return array<string>
     */
    public static function all(): array
    {
        return [
            self::FAMILY,
            self::SEVEN,
            self::HILIFE,
            self::OKMART,
        ];
    }

    /**
     * 取得物流類型名稱。
     *
     * @param string $type 物流類型
     * @return string
     */
    public static function getName(string $type): string
    {
        $names = [
            self::FAMILY => '全家',
            self::SEVEN => '7-ELEVEN',
            self::HILIFE => '萊爾富',
            self::OKMART => 'OK mart',
        ];

        return $names[$type] ?? '未知';
    }
}
