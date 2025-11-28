<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Parameter;

/**
 * 支付類型列舉。
 *
 * 對應藍新金流 PaymentType 回傳參數。
 */
class PaymentType
{
    /** 信用卡 */
    public const CREDIT = 'CREDIT';

    /** 信用卡美國運通 */
    public const CREDITAE = 'CREDITAE';

    /** WebATM */
    public const WEBATM = 'WEBATM';

    /** ATM 轉帳 */
    public const VACC = 'VACC';

    /** 超商代碼繳費 */
    public const CVS = 'CVS';

    /** 超商條碼繳費 */
    public const BARCODE = 'BARCODE';

    /** LINE Pay */
    public const LINEPAY = 'LINEPAY';

    /** 玉山 Wallet */
    public const ESUNWALLET = 'ESUNWALLET';

    /** 台灣 Pay */
    public const TAIWANPAY = 'TAIWANPAY';

    /** BitoPay */
    public const BITOPAY = 'BITOPAY';

    /** 超商取貨付款 */
    public const CVSCOM = 'CVSCOM';

    /** Apple Pay */
    public const APPLEPAY = 'APPLEPAY';

    /** Google Pay */
    public const ANDROIDPAY = 'ANDROIDPAY';

    /** Samsung Pay */
    public const SAMSUNGPAY = 'SAMSUNGPAY';

    /** TWQR */
    public const TWQR = 'TWQR';

    /** 簡單付微信支付 */
    public const EZPWECHAT = 'EZPWECHAT';

    /** 簡單付支付寶 */
    public const EZPALIPAY = 'EZPALIPAY';

    /** 付啦 */
    public const FULA = 'FULA';

    /**
     * 取得所有支付類型。
     *
     * @return array<string>
     */
    public static function all(): array
    {
        return [
            self::CREDIT,
            self::CREDITAE,
            self::WEBATM,
            self::VACC,
            self::CVS,
            self::BARCODE,
            self::LINEPAY,
            self::ESUNWALLET,
            self::TAIWANPAY,
            self::BITOPAY,
            self::CVSCOM,
            self::APPLEPAY,
            self::ANDROIDPAY,
            self::SAMSUNGPAY,
            self::TWQR,
            self::EZPWECHAT,
            self::EZPALIPAY,
            self::FULA,
        ];
    }

    /**
     * 檢查是否為信用卡類型。
     *
     * @param string $type 支付類型
     * @return bool
     */
    public static function isCredit(string $type): bool
    {
        return in_array($type, [self::CREDIT, self::CREDITAE], true);
    }

    /**
     * 檢查是否為 ATM 類型。
     *
     * @param string $type 支付類型
     * @return bool
     */
    public static function isAtm(string $type): bool
    {
        return in_array($type, [self::WEBATM, self::VACC], true);
    }

    /**
     * 檢查是否為超商類型。
     *
     * @param string $type 支付類型
     * @return bool
     */
    public static function isCvs(string $type): bool
    {
        return in_array($type, [self::CVS, self::BARCODE, self::CVSCOM], true);
    }

    /**
     * 檢查是否為電子錢包類型。
     *
     * @param string $type 支付類型
     * @return bool
     */
    public static function isEWallet(string $type): bool
    {
        return in_array($type, [
            self::LINEPAY,
            self::ESUNWALLET,
            self::TAIWANPAY,
            self::BITOPAY,
            self::EZPWECHAT,
            self::EZPALIPAY,
        ], true);
    }
}
