<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Queries;

use CarlLee\NewebPay\Exceptions\NewebPayException;
use CarlLee\NewebPay\Infrastructure\AES256Encoder;
use CarlLee\NewebPay\Infrastructure\CheckValueEncoder;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * 交易查詢。
 *
 * 查詢藍新金流交易訂單狀態。
 */
class QueryOrder
{
    /**
     * API 版本。
     */
    protected string $version = '1.3';

    /**
     * API 請求路徑。
     */
    protected string $requestPath = '/API/QueryTradeInfo';

    /**
     * 特店編號。
     *
     * @var string
     */
    protected string $merchantID;

    /**
     * HashKey。
     *
     * @var string
     */
    protected string $hashKey;

    /**
     * HashIV。
     *
     * @var string
     */
    protected string $hashIV;

    /**
     * 是否為測試環境。
     *
     * @var bool
     */
    protected bool $isTest = false;

    /**
     * HTTP Client。
     *
     * @var Client|null
     */
    protected ?Client $httpClient = null;

    /**
     * 建立查詢物件。
     *
     * @param string $merchantId 特店編號
     * @param string $hashKey HashKey
     * @param string $hashIV HashIV
     */
    public function __construct(string $merchantId, string $hashKey, string $hashIV)
    {
        $this->merchantID = $merchantId;
        $this->hashKey = $hashKey;
        $this->hashIV = $hashIV;
    }

    /**
     * 從設定建立查詢物件。
     *
     * @param string $merchantId 特店編號
     * @param string $hashKey HashKey
     * @param string $hashIV HashIV
     * @return static
     */
    public static function create(string $merchantId, string $hashKey, string $hashIV): self
    {
        return new static($merchantId, $hashKey, $hashIV);
    }

    /**
     * 設定是否為測試環境。
     *
     * @param bool $isTest 是否為測試環境
     * @return static
     */
    public function setTestMode(bool $isTest): self
    {
        $this->isTest = $isTest;

        return $this;
    }

    /**
     * 設定 HTTP Client。
     *
     * @param Client $client HTTP Client
     * @return static
     */
    public function setHttpClient(Client $client): self
    {
        $this->httpClient = $client;

        return $this;
    }

    /**
     * 取得 API 基礎網址。
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->isTest
            ? 'https://ccore.newebpay.com'
            : 'https://core.newebpay.com';
    }

    /**
     * 取得完整 API 網址。
     *
     * @return string
     */
    public function getApiUrl(): string
    {
        return $this->getBaseUrl() . $this->requestPath;
    }

    /**
     * 執行查詢。
     *
     * @param string $merchantOrderNo 特店訂單編號
     * @param int $amt 訂單金額
     * @return array<string, mixed>
     * @throws NewebPayException
     */
    public function query(string $merchantOrderNo, int $amt): array
    {
        $payload = $this->buildPayload($merchantOrderNo, $amt);

        try {
            $client = $this->getHttpClient();
            $response = $client->post($this->getApiUrl(), [
                'form_params' => $payload,
            ]);

            $body = $response->getBody()->getContents();
            $result = json_decode($body, true);

            if ($result === null) {
                throw NewebPayException::apiError('回應格式錯誤');
            }

            return $this->parseResponse($result);
        } catch (GuzzleException $e) {
            throw NewebPayException::apiError('API 請求失敗：' . $e->getMessage());
        }
    }

    /**
     * 建立請求 Payload。
     *
     * @param string $merchantOrderNo 特店訂單編號
     * @param int $amt 訂單金額
     * @return array<string, string>
     */
    protected function buildPayload(string $merchantOrderNo, int $amt): array
    {
        $data = [
            'MerchantID' => $this->merchantID,
            'Version' => $this->version,
            'RespondType' => 'JSON',
            'TimeStamp' => (string) time(),
            'MerchantOrderNo' => $merchantOrderNo,
            'Amt' => $amt,
        ];

        // 產生 CheckValue
        $checkValue = $this->generateCheckValue($merchantOrderNo, $amt);

        return [
            'MerchantID' => $this->merchantID,
            'Version' => $this->version,
            'RespondType' => 'JSON',
            'CheckValue' => $checkValue,
            'TimeStamp' => $data['TimeStamp'],
            'MerchantOrderNo' => $merchantOrderNo,
            'Amt' => (string) $amt,
        ];
    }

    /**
     * 產生查詢用 CheckValue。
     *
     * 查詢 API 的 CheckValue 計算方式與 MPG 不同：
     * SHA256(HashIV={HashIV}&Amt={Amt}&MerchantID={MerchantID}&MerchantOrderNo={MerchantOrderNo}&HashKey={HashKey})
     *
     * @param string $merchantOrderNo 特店訂單編號
     * @param int $amt 訂單金額
     * @return string
     */
    protected function generateCheckValue(string $merchantOrderNo, int $amt): string
    {
        $raw = sprintf(
            'HashIV=%s&Amt=%d&MerchantID=%s&MerchantOrderNo=%s&HashKey=%s',
            $this->hashIV,
            $amt,
            $this->merchantID,
            $merchantOrderNo,
            $this->hashKey
        );

        return strtoupper(hash('sha256', $raw));
    }

    /**
     * 解析回應。
     *
     * @param array<string, mixed> $response 原始回應
     * @return array<string, mixed>
     * @throws NewebPayException
     */
    protected function parseResponse(array $response): array
    {
        $status = $response['Status'] ?? '';
        $message = $response['Message'] ?? '';

        if ($status !== 'SUCCESS') {
            throw NewebPayException::apiError($message, $status);
        }

        return $response['Result'] ?? [];
    }

    /**
     * 取得 HTTP Client。
     *
     * @return Client
     */
    protected function getHttpClient(): Client
    {
        if ($this->httpClient === null) {
            $this->httpClient = new Client([
                'timeout' => 30,
                'verify' => true,
            ]);
        }

        return $this->httpClient;
    }
}
