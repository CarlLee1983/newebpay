<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Queries;

use CarlLee\NewebPay\Exceptions\NewebPayException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * 信用卡交易明細查詢。
 *
 * 查詢信用卡交易的詳細資訊。
 */
class QueryCreditDetail
{
    /**
     * API 版本。
     */
    protected string $version = '1.0';

    /**
     * API 請求路徑。
     */
    protected string $requestPath = '/API/CreditCard/TradeDetail';

    /**
     * 是否為測試環境。
     */
    protected bool $isTest = false;

    /**
     * HTTP Client。
     */
    protected ?Client $httpClient = null;

    /**
     * 建立查詢物件。
     *
     * @param string $merchantId 特店編號
     * @param string $hashKey HashKey
     * @param string $hashIV HashIV
     */
    public function __construct(
        protected string $merchantId,
        protected string $hashKey,
        protected string $hashIV,
    ) {
    }

    /**
     * 從設定建立查詢物件。
     *
     * @param string $merchantId 特店編號
     * @param string $hashKey HashKey
     * @param string $hashIV HashIV
     * @return static
     */
    public static function create(string $merchantId, string $hashKey, string $hashIV): static
    {
        return new static($merchantId, $hashKey, $hashIV);
    }

    /**
     * 設定是否為測試環境。
     *
     * @param bool $isTest 是否為測試環境
     * @return static
     */
    public function setTestMode(bool $isTest): static
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
    public function setHttpClient(Client $client): static
    {
        $this->httpClient = $client;

        return $this;
    }

    /**
     * 取得 API 基礎網址。
     */
    public function getBaseUrl(): string
    {
        return $this->isTest
            ? 'https://ccore.newebpay.com'
            : 'https://core.newebpay.com';
    }

    /**
     * 取得完整 API 網址。
     */
    public function getApiUrl(): string
    {
        return $this->getBaseUrl() . $this->requestPath;
    }

    /**
     * 依特店訂單編號查詢。
     *
     * @param string $merchantOrderNo 特店訂單編號
     * @param int $amt 訂單金額
     * @return array<string, mixed>
     * @throws NewebPayException
     */
    public function queryByOrderNo(string $merchantOrderNo, int $amt): array
    {
        return $this->query([
            'MerchantOrderNo' => $merchantOrderNo,
            'Amt' => $amt,
        ]);
    }

    /**
     * 依藍新交易序號查詢。
     *
     * @param string $tradeNo 藍新交易序號
     * @param int $amt 訂單金額
     * @return array<string, mixed>
     * @throws NewebPayException
     */
    public function queryByTradeNo(string $tradeNo, int $amt): array
    {
        return $this->query([
            'TradeNo' => $tradeNo,
            'Amt' => $amt,
        ]);
    }

    /**
     * 執行查詢。
     *
     * @param array<string, mixed> $params 查詢參數
     * @return array<string, mixed>
     * @throws NewebPayException
     */
    protected function query(array $params): array
    {
        $payload = $this->buildPayload($params);

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
     * @param array<string, mixed> $params 查詢參數
     * @return array<string, string>
     */
    protected function buildPayload(array $params): array
    {
        $data = array_merge([
            'MerchantID' => $this->merchantId,
            'Version' => $this->version,
            'RespondType' => 'JSON',
            'TimeStamp' => (string) time(),
        ], $params);

        // 產生 CheckValue
        $checkValue = $this->generateCheckValue($params);

        return array_merge($data, ['CheckValue' => $checkValue]);
    }

    /**
     * 產生查詢用 CheckValue。
     *
     * @param array<string, mixed> $params 查詢參數
     * @return string
     */
    protected function generateCheckValue(array $params): string
    {
        $amt = $params['Amt'] ?? 0;

        if (isset($params['MerchantOrderNo'])) {
            $raw = sprintf(
                'HashIV=%s&Amt=%d&MerchantID=%s&MerchantOrderNo=%s&HashKey=%s',
                $this->hashIV,
                $amt,
                $this->merchantId,
                $params['MerchantOrderNo'],
                $this->hashKey
            );
        } else {
            $raw = sprintf(
                'HashIV=%s&Amt=%d&MerchantID=%s&TradeNo=%s&HashKey=%s',
                $this->hashIV,
                $amt,
                $this->merchantId,
                $params['TradeNo'] ?? '',
                $this->hashKey
            );
        }

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
     */
    protected function getHttpClient(): Client
    {
        return $this->httpClient ??= new Client([
            'timeout' => 30,
            'verify' => true,
        ]);
    }
}
