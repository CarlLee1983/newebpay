<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Infrastructure;

use CarlLee\NewebPay\Exceptions\NewebPayException;

/**
 * CheckValue (TradeSha) 編碼器。
 *
 * 依據藍新金流技術文件 4.1.5 CheckValue 規範實作。
 * 用於驗證交易資料的完整性。
 */
class CheckValueEncoder
{
    /**
     * HashKey。
     *
     * @var string
     */
    private string $hashKey;

    /**
     * HashIV。
     *
     * @var string
     */
    private string $hashIV;

    /**
     * 建立編碼器。
     *
     * @param string $hashKey HashKey
     * @param string $hashIV HashIV
     */
    public function __construct(string $hashKey, string $hashIV)
    {
        $this->hashKey = $hashKey;
        $this->hashIV = $hashIV;
    }

    /**
     * 產生 CheckValue (TradeSha)。
     *
     * 計算方式：SHA256(TradeInfo + HashKey + HashIV)
     *
     * @param string $tradeInfo 加密後的 TradeInfo 字串
     * @return string 大寫的 SHA256 雜湊值
     */
    public function generate(string $tradeInfo): string
    {
        // SHA256(TradeInfo + HashKey + HashIV) 並轉大寫
        $raw = $tradeInfo . $this->hashKey . $this->hashIV;

        return strtoupper(hash('sha256', $raw));
    }

    /**
     * 驗證 CheckValue (TradeSha)。
     *
     * @param string $tradeInfo 加密後的 TradeInfo 字串
     * @param string $tradeSha 收到的 TradeSha 值
     * @return bool
     */
    public function verify(string $tradeInfo, string $tradeSha): bool
    {
        $calculated = $this->generate($tradeInfo);

        return strtoupper($tradeSha) === $calculated;
    }

    /**
     * 驗證並拋出例外。
     *
     * @param string $tradeInfo 加密後的 TradeInfo 字串
     * @param string $tradeSha 收到的 TradeSha 值
     * @return void
     * @throws NewebPayException 當驗證失敗時
     */
    public function verifyOrFail(string $tradeInfo, string $tradeSha): void
    {
        if (!$this->verify($tradeInfo, $tradeSha)) {
            throw NewebPayException::checkValueFailed();
        }
    }

    /**
     * 從設定建立編碼器。
     *
     * @param string $hashKey HashKey
     * @param string $hashIV HashIV
     * @return static
     */
    public static function create(string $hashKey, string $hashIV): self
    {
        return new static($hashKey, $hashIV);
    }
}
