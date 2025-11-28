<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Actions;

use CarlLee\NewebPay\Exceptions\NewebPayException;
use CarlLee\NewebPay\Infrastructure\AES256Encoder;
use CarlLee\NewebPay\Infrastructure\CheckValueEncoder;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * 信用卡取消授權。
 *
 * 取消尚未請款的信用卡交易授權。
 */
class CreditCancel
{
    /**
     * API 版本。
     */
    protected string $version = '1.0';

    /**
     * API 請求路徑。
     */
    protected string $requestPath = '/API/CreditCard/Cancel';

    /**
     * 是否為測試環境。
     */
    protected bool $isTest = false;

    /**
     * AES256 編碼器。
     */
    protected ?AES256Encoder $aesEncoder = null;

    /**
     * CheckValue 編碼器。
     */
    protected ?CheckValueEncoder $checkValueEncoder = null;

    /**
     * HTTP Client。
     */
    protected ?Client $httpClient = null;

    /**
     * 建立取消授權物件。
     *
     * @param string $merchantId 特店編號
     * @param string $hashKey HashKey
     * @param string $hashIV HashIV
     */
    public function __construct(
        protected string $merchantId,
        protected string $hashKey,
        protected string $hashIV,
    ) {}

    /**
     * 從設定建立取消授權物件。
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
     * 執行取消授權。
     *
     * @param string $merchantOrderNo 特店訂單編號
     * @param int $amt 訂單金額
     * @param string $indexType 依據類型（1=藍新交易序號, 2=特店訂單編號）
     * @param string|null $tradeNo 藍新交易序號（當 indexType=1 時必填）
     * @return array<string, mixed>
     * @throws NewebPayException
     */
    public function cancel(
        string $merchantOrderNo,
        int $amt,
        string $indexType = '2',
        ?string $tradeNo = null
    ): array {
        $postData = [
            'RespondType' => 'JSON',
            'Version' => $this->version,
            'Amt' => $amt,
            'MerchantOrderNo' => $merchantOrderNo,
            'IndexType' => $indexType,
            'TimeStamp' => (string) time(),
        ];

        if ($indexType === '1' && $tradeNo !== null) {
            $postData['TradeNo'] = $tradeNo;
        }

        $payload = $this->buildPayload($postData);

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
     * @param array<string, mixed> $postData 請求資料
     * @return array<string, string>
     */
    protected function buildPayload(array $postData): array
    {
        $encoder = $this->getAesEncoder();

        $tradeInfo = $encoder->encrypt($postData);

        return [
            'MerchantID_' => $this->merchantId,
            'PostData_' => $tradeInfo,
        ];
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
     * 取得 AES256 編碼器。
     */
    protected function getAesEncoder(): AES256Encoder
    {
        return $this->aesEncoder ??= new AES256Encoder($this->hashKey, $this->hashIV);
    }

    /**
     * 取得 CheckValue 編碼器。
     */
    protected function getCheckValueEncoder(): CheckValueEncoder
    {
        return $this->checkValueEncoder ??= new CheckValueEncoder($this->hashKey, $this->hashIV);
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
