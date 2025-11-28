<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Operations;

use CarlLee\NewebPay\Content;
use CarlLee\NewebPay\Exceptions\NewebPayException;

/**
 * LINE Pay 支付。
 *
 * 支援 LINE Pay 電子錢包付款。
 */
class LinePayPayment extends Content
{
    /**
     * 商品圖片網址最大長度。
     */
    public const IMAGE_URL_MAX_LENGTH = 500;

    /**
     * @inheritDoc
     */
    protected function initContent(): void
    {
        parent::initContent();

        // 啟用 LINE Pay
        $this->content['LINEPAY'] = 1;
    }

    /**
     * 設定商品圖片網址。
     *
     * @param string $url 圖片網址
     * @return static
     */
    public function setImageUrl(string $url): self
    {
        if (strlen($url) > self::IMAGE_URL_MAX_LENGTH) {
            throw NewebPayException::tooLong('ImageUrl', self::IMAGE_URL_MAX_LENGTH);
        }

        $this->content['ImageUrl'] = $url;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function validation(): void
    {
        $this->validateBaseParams();
    }
}
