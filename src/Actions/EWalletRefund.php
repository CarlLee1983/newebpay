<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Actions;

use CarlLee\NewebPay\Exceptions\NewebPayException;
use CarlLee\NewebPay\Infrastructure\AES256Encoder;
use CarlLee\NewebPay\Infrastructure\CheckValueEncoder;
use CarlLee\NewebPay\Parameter\PaymentType;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * 電子錢包退款。
 *
 * 對電子錢包交易（LINE Pay、玉山 Wallet、台灣 Pay 等）進行退款。
 * 注意：此 API 使用 JSON Encode，與其他 API 不同。
 */
class EWalletRefund
{
    /**
     * API 版本。
     */
    protected string $version = '1.0';

    /**
     * API 請求路徑。
     */
    protected string $requestPath = '/API/EWallet/Refund';

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
     * 建立退款物件。
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
     * 從設定建立退款物件。
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
     * 執行退款。
     *
     * @param string $merchantOrderNo 特店訂單編號
     * @param int $amt 退款金額
     * @param string|PaymentType $paymentType 付款方式（LINEPAY, ESUNWALLET, TAIWANPAY 等）或 PaymentType 列舉
     * @return array<string, mixed>
     * @throws NewebPayException
     */
    public function refund(string $merchantOrderNo, int $amt, string|PaymentType $paymentType): array
    {
        $paymentTypeValue = $paymentType instanceof PaymentType ? $paymentType->value : $paymentType;

        $postData = [
            'MerchantID' => $this->merchantId,
            'MerchantOrderNo' => $merchantOrderNo,
            'Amount' => $amt,
            'PaymentType' => $paymentTypeValue,
            'TimeStamp' => (string) time(),
        ];

        $payload = $this->buildPayload($postData);

        try {
            $client = $this->getHttpClient();
            $response = $client->post($this->getApiUrl(), [
                'json' => $payload,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
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
     * 電子錢包退款 API 使用 JSON 格式。
     *
     * @param array<string, mixed> $postData 請求資料
     * @return array<string, string>
     */
    protected function buildPayload(array $postData): array
    {
        $encoder = $this->getAesEncoder();
        $checkValueEncoder = $this->getCheckValueEncoder();

        $tradeInfo = $encoder->encrypt($postData);
        $tradeSha = $checkValueEncoder->generate($tradeInfo);

        return [
            'MerchantID_' => $this->merchantId,
            'PostData_' => $tradeInfo,
            'Pos_' => $tradeSha,
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
