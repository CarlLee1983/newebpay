<?php

declare(strict_types=1);

namespace CarlLee\NewebPay;

use CarlLee\NewebPay\Contracts\PaymentInterface;
use CarlLee\NewebPay\Exceptions\NewebPayException;
use CarlLee\NewebPay\Infrastructure\AES256Encoder;
use CarlLee\NewebPay\Infrastructure\CheckValueEncoder;

/**
 * 藍新金流 Content 基礎類別。
 *
 * 所有 MPG 支付操作類別的基類。
 */
abstract class Content implements PaymentInterface
{
    /**
     * 特店訂單編號最大長度。
     */
    public const int MERCHANT_ORDER_NO_MAX_LENGTH = 30;

    /**
     * 商品資訊最大長度。
     */
    public const int ITEM_DESC_MAX_LENGTH = 50;

    /**
     * Email 最大長度。
     */
    public const int EMAIL_MAX_LENGTH = 50;

    /**
     * MPG API 版本。
     */
    protected string $version = '2.0';

    /**
     * API 請求路徑。
     */
    protected string $requestPath = '/MPG/mpg_gateway';

    /**
     * 是否為測試環境。
     */
    protected bool $isTest = false;

    /**
     * 內容資料。
     *
     * @var array<string, mixed>
     */
    protected array $content = [];

    /**
     * AES256 編碼器。
     */
    protected ?AES256Encoder $aesEncoder = null;

    /**
     * CheckValue 編碼器。
     */
    protected ?CheckValueEncoder $checkValueEncoder = null;

    /**
     * 建立 Content 實例。
     *
     * @param string $merchantId 特店編號
     * @param string $hashKey HashKey
     * @param string $hashIV HashIV
     */
    public function __construct(
        protected string $merchantId = '',
        protected string $hashKey = '',
        protected string $hashIV = '',
    ) {
        $this->initContent();
    }

    /**
     * 初始化內容。
     */
    protected function initContent(): void
    {
        $this->content = [
            'MerchantID' => $this->merchantId,
            'MerchantOrderNo' => '',
            'TimeStamp' => (string) time(),
            'Version' => $this->version,
            'Amt' => 0,
            'ItemDesc' => '',
            'RespondType' => 'JSON',
            'LangType' => 'zh-tw',
        ];
    }

    /**
     * 設定特店編號。
     *
     * @param string $id 特店編號
     * @return static
     */
    public function setMerchantID(string $id): static
    {
        $this->merchantId = $id;
        $this->content['MerchantID'] = $id;

        return $this;
    }

    /**
     * 取得特店編號。
     */
    public function getMerchantID(): string
    {
        return $this->merchantId;
    }

    /**
     * 設定 HashKey。
     *
     * @param string $key HashKey
     * @return static
     */
    public function setHashKey(string $key): static
    {
        $this->hashKey = $key;

        return $this;
    }

    /**
     * 設定 HashIV。
     *
     * @param string $iv HashIV
     * @return static
     */
    public function setHashIV(string $iv): static
    {
        $this->hashIV = $iv;

        return $this;
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
     * 是否為測試環境。
     */
    public function isTestMode(): bool
    {
        return $this->isTest;
    }

    /**
     * @inheritDoc
     */
    public function setMerchantOrderNo(string $orderNo): static
    {
        if (strlen($orderNo) > self::MERCHANT_ORDER_NO_MAX_LENGTH) {
            throw NewebPayException::tooLong('MerchantOrderNo', self::MERCHANT_ORDER_NO_MAX_LENGTH);
        }

        $this->content['MerchantOrderNo'] = $orderNo;

        return $this;
    }

    /**
     * 設定時間戳記。
     *
     * @param int|string $timestamp Unix 時間戳記
     * @return static
     */
    public function setTimeStamp(int|string $timestamp): static
    {
        $this->content['TimeStamp'] = (string) $timestamp;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setAmt(int $amount): static
    {
        if ($amount <= 0) {
            throw NewebPayException::invalid('Amt', '金額必須大於 0');
        }

        $this->content['Amt'] = $amount;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setItemDesc(string $desc): static
    {
        if (strlen($desc) > self::ITEM_DESC_MAX_LENGTH) {
            throw NewebPayException::tooLong('ItemDesc', self::ITEM_DESC_MAX_LENGTH);
        }

        $this->content['ItemDesc'] = $desc;

        return $this;
    }

    /**
     * 設定交易限制秒數。
     *
     * @param int $seconds 秒數 (60-900)
     * @return static
     */
    public function setTradeLimit(int $seconds): static
    {
        if ($seconds < 60 || $seconds > 900) {
            throw NewebPayException::invalid('TradeLimit', '限制秒數必須在 60-900 之間');
        }

        $this->content['TradeLimit'] = $seconds;

        return $this;
    }

    /**
     * 設定繳費有效期限。
     *
     * @param string $expireDate 格式 Y-m-d
     * @return static
     */
    public function setExpireDate(string $expireDate): static
    {
        $this->content['ExpireDate'] = $expireDate;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setReturnURL(string $url): static
    {
        $this->content['ReturnURL'] = $url;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setNotifyURL(string $url): static
    {
        $this->content['NotifyURL'] = $url;

        return $this;
    }

    /**
     * 設定取號完成返回網址。
     *
     * @param string $url 網址
     * @return static
     */
    public function setCustomerURL(string $url): static
    {
        $this->content['CustomerURL'] = $url;

        return $this;
    }

    /**
     * 設定返回商店網址。
     *
     * @param string $url 網址
     * @return static
     */
    public function setClientBackURL(string $url): static
    {
        $this->content['ClientBackURL'] = $url;

        return $this;
    }

    /**
     * 設定付款人電子信箱。
     *
     * @param string $email Email
     * @return static
     */
    public function setEmail(string $email): static
    {
        if (strlen($email) > self::EMAIL_MAX_LENGTH) {
            throw NewebPayException::tooLong('Email', self::EMAIL_MAX_LENGTH);
        }

        $this->content['Email'] = $email;

        return $this;
    }

    /**
     * 設定是否開啟付款人資料修改。
     *
     * @param int $modify 0=不可修改, 1=可修改
     * @return static
     */
    public function setEmailModify(int $modify): static
    {
        $this->content['EmailModify'] = $modify;

        return $this;
    }

    /**
     * 設定商店備註。
     *
     * @param string $orderComment 備註
     * @return static
     */
    public function setOrderComment(string $orderComment): static
    {
        $this->content['OrderComment'] = $orderComment;

        return $this;
    }

    /**
     * 設定語系。
     *
     * @param string $lang zh-tw 或 en
     * @return static
     */
    public function setLangType(string $lang): static
    {
        $this->content['LangType'] = $lang;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRequestPath(): string
    {
        return $this->requestPath;
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
        return $this->getBaseUrl() . $this->getRequestPath();
    }

    /**
     * 取得 AES256 編碼器。
     */
    public function getAesEncoder(): AES256Encoder
    {
        return $this->aesEncoder ??= new AES256Encoder($this->hashKey, $this->hashIV);
    }

    /**
     * 取得 CheckValue 編碼器。
     */
    public function getCheckValueEncoder(): CheckValueEncoder
    {
        return $this->checkValueEncoder ??= new CheckValueEncoder($this->hashKey, $this->hashIV);
    }

    /**
     * 驗證內容資料。
     *
     * @throws NewebPayException 當驗證失敗時
     */
    abstract protected function validation(): void;

    /**
     * 驗證基礎參數。
     *
     * @throws NewebPayException 當驗證失敗時
     */
    protected function validateBaseParams(): void
    {
        if (empty($this->merchantId)) {
            throw NewebPayException::required('MerchantID');
        }

        if (empty($this->content['MerchantOrderNo'])) {
            throw NewebPayException::required('MerchantOrderNo');
        }

        if (empty($this->content['Amt']) || $this->content['Amt'] <= 0) {
            throw NewebPayException::required('Amt');
        }

        if (empty($this->content['ItemDesc'])) {
            throw NewebPayException::required('ItemDesc');
        }
    }

    /**
     * @inheritDoc
     */
    public function getPayload(): array
    {
        $this->validation();

        // 同步 MerchantID
        $this->content['MerchantID'] = $this->merchantId;

        // 過濾空值
        return array_filter($this->content, fn($value) => $value !== '' && $value !== null);
    }

    /**
     * @inheritDoc
     */
    public function getContent(): array
    {
        $payload = $this->getPayload();
        $encoder = $this->getAesEncoder();
        $checkValueEncoder = $this->getCheckValueEncoder();

        // 1. 加密 TradeInfo
        $tradeInfo = $encoder->encrypt($payload);

        // 2. 產生 TradeSha
        $tradeSha = $checkValueEncoder->generate($tradeInfo);

        return [
            'MerchantID' => $this->merchantId,
            'TradeInfo' => $tradeInfo,
            'TradeSha' => $tradeSha,
            'Version' => $this->version,
        ];
    }

    /**
     * 取得原始內容陣列。
     *
     * @return array<string, mixed>
     */
    public function getRawContent(): array
    {
        return $this->content;
    }

    /**
     * 設定自訂內容。
     *
     * @param string $key 鍵
     * @param mixed $value 值
     * @return static
     */
    public function set(string $key, mixed $value): static
    {
        $this->content[$key] = $value;

        return $this;
    }

    /**
     * 取得內容值。
     *
     * @param string $key 鍵
     * @param mixed $default 預設值
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->content[$key] ?? $default;
    }
}
