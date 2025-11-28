<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Infrastructure;

use CarlLee\NewebPay\Exceptions\NewebPayException;

/**
 * AES-256-CBC 加解密器。
 *
 * 依據藍新金流技術文件 4.1.1 AES256 加密規範實作。
 */
class AES256Encoder
{
    /**
     * 加密演算法。
     */
    private const CIPHER_METHOD = 'AES-256-CBC';

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
     * 建立加解密器。
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
     * 加密資料。
     *
     * 將資料陣列轉換為 URL 編碼查詢字串後進行 AES-256-CBC 加密。
     *
     * @param array<string, mixed> $data 原始資料
     * @return string 加密後的十六進位字串
     */
    public function encrypt(array $data): string
    {
        // 1. 將參數組成 URL 編碼查詢字串
        $queryString = http_build_query($data);

        // 2. 使用 AES-256-CBC 加密
        $encrypted = openssl_encrypt(
            $queryString,
            self::CIPHER_METHOD,
            $this->hashKey,
            OPENSSL_RAW_DATA,
            $this->hashIV
        );

        if ($encrypted === false) {
            throw NewebPayException::apiError('AES 加密失敗');
        }

        // 3. 轉換為十六進位字串
        return bin2hex($encrypted);
    }

    /**
     * 解密資料。
     *
     * 將十六進位加密字串解密為資料陣列。
     *
     * @param string $tradeInfo 加密的 TradeInfo 字串
     * @return array<string, mixed> 解密後的資料陣列
     * @throws NewebPayException 當解密失敗時
     */
    public function decrypt(string $tradeInfo): array
    {
        // 檢查是否為有效的十六進位字串
        if (!ctype_xdigit($tradeInfo) || strlen($tradeInfo) % 2 !== 0) {
            throw NewebPayException::decryptFailed();
        }

        // 1. 將十六進位字串轉換為二進位
        $binary = hex2bin($tradeInfo);

        if ($binary === false) {
            throw NewebPayException::decryptFailed();
        }

        // 2. 使用 AES-256-CBC 解密
        $decrypted = openssl_decrypt(
            $binary,
            self::CIPHER_METHOD,
            $this->hashKey,
            OPENSSL_RAW_DATA,
            $this->hashIV
        );

        if ($decrypted === false) {
            throw NewebPayException::decryptFailed();
        }

        // 3. 解析 URL 編碼查詢字串
        parse_str($decrypted, $result);

        return $result;
    }

    /**
     * 從設定建立加解密器。
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
