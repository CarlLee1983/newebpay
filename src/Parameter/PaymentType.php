<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Parameter;

/**
 * 支付類型列舉。
 *
 * 對應藍新金流 PaymentType 回傳參數。
 */
enum PaymentType: string
{
    /** 信用卡 */
    case Credit = 'CREDIT';

    /** 信用卡美國運通 */
    case CreditAE = 'CREDITAE';

    /** WebATM */
    case WebAtm = 'WEBATM';

    /** ATM 轉帳 */
    case Vacc = 'VACC';

    /** 超商代碼繳費 */
    case Cvs = 'CVS';

    /** 超商條碼繳費 */
    case Barcode = 'BARCODE';

    /** LINE Pay */
    case LinePay = 'LINEPAY';

    /** 玉山 Wallet */
    case EsunWallet = 'ESUNWALLET';

    /** 台灣 Pay */
    case TaiwanPay = 'TAIWANPAY';

    /** BitoPay */
    case BitoPay = 'BITOPAY';

    /** 超商取貨付款 */
    case Cvscom = 'CVSCOM';

    /** Apple Pay */
    case ApplePay = 'APPLEPAY';

    /** Google Pay */
    case AndroidPay = 'ANDROIDPAY';

    /** Samsung Pay */
    case SamsungPay = 'SAMSUNGPAY';

    /** TWQR */
    case Twqr = 'TWQR';

    /** 簡單付微信支付 */
    case EzpWechat = 'EZPWECHAT';

    /** 簡單付支付寶 */
    case EzpAlipay = 'EZPALIPAY';

    /** 付啦 */
    case Fula = 'FULA';

    /**
     * 檢查是否為信用卡類型。
     */
    public function isCredit(): bool
    {
        return in_array($this, [self::Credit, self::CreditAE], true);
    }

    /**
     * 檢查是否為 ATM 類型。
     */
    public function isAtm(): bool
    {
        return in_array($this, [self::WebAtm, self::Vacc], true);
    }

    /**
     * 檢查是否為超商類型。
     */
    public function isCvs(): bool
    {
        return in_array($this, [self::Cvs, self::Barcode, self::Cvscom], true);
    }

    /**
     * 檢查是否為電子錢包類型。
     */
    public function isEWallet(): bool
    {
        return in_array($this, [
            self::LinePay,
            self::EsunWallet,
            self::TaiwanPay,
            self::BitoPay,
            self::EzpWechat,
            self::EzpAlipay,
        ], true);
    }

    /**
     * 從字串值建立列舉（靜態方法保持向後相容）。
     */
    public static function fromString(string $value): ?self
    {
        return self::tryFrom($value);
    }

    /**
     * 取得所有支付類型值。
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
