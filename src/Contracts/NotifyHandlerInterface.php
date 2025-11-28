<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Contracts;

/**
 * 通知處理器介面。
 */
interface NotifyHandlerInterface
{
    /**
     * 驗證通知資料。
     *
     * @param array<string, mixed> $data 通知資料
     * @return bool
     */
    public function verify(array $data): bool;

    /**
     * 取得通知資料。
     *
     * @return array<string, mixed>
     */
    public function getData(): array;

    /**
     * 檢查交易是否成功。
     *
     * @return bool
     */
    public function isSuccess(): bool;

    /**
     * 取得回應狀態。
     *
     * @return string
     */
    public function getStatus(): string;

    /**
     * 取得回應訊息。
     *
     * @return string
     */
    public function getMessage(): string;
}
