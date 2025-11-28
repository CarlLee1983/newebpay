<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Notifications;

use CarlLee\NewebPay\Contracts\NotifyHandlerInterface;
use CarlLee\NewebPay\Exceptions\NewebPayException;
use CarlLee\NewebPay\Infrastructure\AES256Encoder;
use CarlLee\NewebPay\Infrastructure\CheckValueEncoder;

/**
 * 支付完成通知處理器。
 *
 * 處理藍新金流 ReturnURL / NotifyURL 回傳的支付結果通知。
 */
class PaymentNotify implements NotifyHandlerInterface
{
    /**
     * AES256 編碼器。
     */
    private readonly AES256Encoder $aesEncoder;

    /**
     * CheckValue 編碼器。
     */
    private readonly CheckValueEncoder $checkValueEncoder;

    /**
     * 原始通知資料。
     *
     * @var array<string, mixed>
     */
    private array $rawData = [];

    /**
     * 解密後的交易資料。
     *
     * @var array<string, mixed>
     */
    private array $data = [];

    /**
     * 是否已驗證。
     */
    private bool $verified = false;

    /**
     * 建立通知處理器。
     *
     * @param string $hashKey HashKey
     * @param string $hashIV HashIV
     */
    public function __construct(string $hashKey, string $hashIV)
    {
        $this->aesEncoder = new AES256Encoder($hashKey, $hashIV);
        $this->checkValueEncoder = new CheckValueEncoder($hashKey, $hashIV);
    }

    /**
     * 從設定建立通知處理器。
     *
     * @param string $hashKey HashKey
     * @param string $hashIV HashIV
     * @return static
     */
    public static function create(string $hashKey, string $hashIV): static
    {
        return new static($hashKey, $hashIV);
    }

    /**
     * @inheritDoc
     */
    public function verify(array $data): bool
    {
        $this->rawData = $data;

        // 檢查必要欄位
        if (!isset($data['TradeInfo']) || !isset($data['TradeSha'])) {
            return false;
        }

        // 驗證 TradeSha
        if (!$this->checkValueEncoder->verify($data['TradeInfo'], $data['TradeSha'])) {
            return false;
        }

        // 解密 TradeInfo
        try {
            $this->data = $this->aesEncoder->decrypt($data['TradeInfo']);
            $this->verified = true;

            return true;
        } catch (NewebPayException) {
            return false;
        }
    }

    /**
     * 驗證並拋出例外。
     *
     * @param array<string, mixed> $data 通知資料
     * @return static
     * @throws NewebPayException 當驗證失敗時
     */
    public function verifyOrFail(array $data): static
    {
        if (!$this->verify($data)) {
            throw NewebPayException::checkValueFailed();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * 取得原始通知資料。
     *
     * @return array<string, mixed>
     */
    public function getRawData(): array
    {
        return $this->rawData;
    }

    /**
     * @inheritDoc
     */
    public function isSuccess(): bool
    {
        return $this->getStatus() === 'SUCCESS';
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): string
    {
        return (string) ($this->data['Status'] ?? '');
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): string
    {
        return (string) ($this->data['Message'] ?? '');
    }

    /**
     * 取得特店編號。
     */
    public function getMerchantID(): string
    {
        return (string) ($this->data['MerchantID'] ?? '');
    }

    /**
     * 取得特店訂單編號。
     */
    public function getMerchantOrderNo(): string
    {
        $result = $this->data['Result'] ?? [];

        return (string) ($result['MerchantOrderNo'] ?? '');
    }

    /**
     * 取得藍新金流交易序號。
     */
    public function getTradeNo(): string
    {
        $result = $this->data['Result'] ?? [];

        return (string) ($result['TradeNo'] ?? '');
    }

    /**
     * 取得交易金額。
     */
    public function getAmt(): int
    {
        $result = $this->data['Result'] ?? [];

        return (int) ($result['Amt'] ?? 0);
    }

    /**
     * 取得支付方式。
     */
    public function getPaymentType(): string
    {
        $result = $this->data['Result'] ?? [];

        return (string) ($result['PaymentType'] ?? '');
    }

    /**
     * 取得交易時間。
     */
    public function getPayTime(): string
    {
        $result = $this->data['Result'] ?? [];

        return (string) ($result['PayTime'] ?? '');
    }

    /**
     * 取得 IP 位址。
     */
    public function getIP(): string
    {
        $result = $this->data['Result'] ?? [];

        return (string) ($result['IP'] ?? '');
    }

    /**
     * 取得付款銀行。
     */
    public function getPayBankCode(): string
    {
        $result = $this->data['Result'] ?? [];

        return (string) ($result['PayBankCode'] ?? '');
    }

    /**
     * 取得授權碼（信用卡）。
     */
    public function getAuthCode(): string
    {
        $result = $this->data['Result'] ?? [];

        return (string) ($result['Auth'] ?? '');
    }

    /**
     * 取得卡號末四碼（信用卡）。
     */
    public function getCard4No(): string
    {
        $result = $this->data['Result'] ?? [];

        return (string) ($result['Card4No'] ?? '');
    }

    /**
     * 取得卡號前六碼（信用卡）。
     */
    public function getCard6No(): string
    {
        $result = $this->data['Result'] ?? [];

        return (string) ($result['Card6No'] ?? '');
    }

    /**
     * 取得 ECI 值（3D 驗證）。
     */
    public function getECI(): string
    {
        $result = $this->data['Result'] ?? [];

        return (string) ($result['ECI'] ?? '');
    }

    /**
     * 取得分期期數。
     */
    public function getInst(): int
    {
        $result = $this->data['Result'] ?? [];

        return (int) ($result['Inst'] ?? 0);
    }

    /**
     * 取得首期金額。
     */
    public function getInstFirst(): int
    {
        $result = $this->data['Result'] ?? [];

        return (int) ($result['InstFirst'] ?? 0);
    }

    /**
     * 取得每期金額。
     */
    public function getInstEach(): int
    {
        $result = $this->data['Result'] ?? [];

        return (int) ($result['InstEach'] ?? 0);
    }

    /**
     * 取得交易結果物件。
     *
     * @return array<string, mixed>
     */
    public function getResult(): array
    {
        return $this->data['Result'] ?? [];
    }

    /**
     * 是否已驗證。
     */
    public function isVerified(): bool
    {
        return $this->verified;
    }
}
