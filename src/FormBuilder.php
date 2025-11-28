<?php

declare(strict_types=1);

namespace CarlLee\NewebPay;

use CarlLee\NewebPay\Contracts\PaymentInterface;

/**
 * HTML Form 產生器。
 *
 * 產生送往藍新金流的 HTML 表單。
 */
class FormBuilder
{
    /**
     * 表單 ID。
     */
    private string $formId = 'newebpay-form';

    /**
     * 是否自動送出。
     */
    private bool $autoSubmit = true;

    /**
     * 送出按鈕文字。
     */
    private string $submitText = '前往付款';

    /**
     * 建立表單產生器。
     *
     * @param PaymentInterface $payment 支付操作物件
     */
    public function __construct(
        private readonly PaymentInterface $payment,
    ) {}

    /**
     * 從支付操作建立表單產生器。
     *
     * @param PaymentInterface $payment 支付操作物件
     * @return static
     */
    public static function create(PaymentInterface $payment): static
    {
        return new static($payment);
    }

    /**
     * 設定表單 ID。
     *
     * @param string $id 表單 ID
     * @return static
     */
    public function setFormId(string $id): static
    {
        $this->formId = $id;

        return $this;
    }

    /**
     * 設定是否自動送出。
     *
     * @param bool $autoSubmit 是否自動送出
     * @return static
     */
    public function setAutoSubmit(bool $autoSubmit): static
    {
        $this->autoSubmit = $autoSubmit;

        return $this;
    }

    /**
     * 設定送出按鈕文字。
     *
     * @param string $text 按鈕文字
     * @return static
     */
    public function setSubmitText(string $text): static
    {
        $this->submitText = $text;

        return $this;
    }

    /**
     * 產生 HTML 表單。
     */
    public function build(): string
    {
        $content = $this->payment->getContent();
        $url = $this->getFormAction();

        $html = $this->buildFormOpen($url);
        $html .= $this->buildHiddenFields($content);
        $html .= $this->buildSubmitButton();
        $html .= $this->buildFormClose();

        if ($this->autoSubmit) {
            $html .= $this->buildAutoSubmitScript();
        }

        return $html;
    }

    /**
     * 產生表單開頭。
     *
     * @param string $url 表單 action URL
     */
    private function buildFormOpen(string $url): string
    {
        return sprintf(
            '<form id="%s" method="post" action="%s">',
            htmlspecialchars($this->formId, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($url, ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * 產生隱藏欄位。
     *
     * @param array<string, mixed> $content 內容
     */
    private function buildHiddenFields(array $content): string
    {
        $html = '';

        foreach ($content as $key => $value) {
            $html .= sprintf(
                '<input type="hidden" name="%s" value="%s">',
                htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8')
            );
        }

        return $html;
    }

    /**
     * 產生送出按鈕。
     */
    private function buildSubmitButton(): string
    {
        $style = $this->autoSubmit ? ' style="display:none;"' : '';

        return sprintf(
            '<button type="submit"%s>%s</button>',
            $style,
            htmlspecialchars($this->submitText, ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * 產生表單結尾。
     */
    private function buildFormClose(): string
    {
        return '</form>';
    }

    /**
     * 產生自動送出 JavaScript。
     */
    private function buildAutoSubmitScript(): string
    {
        return sprintf(
            '<script>document.getElementById("%s").submit();</script>',
            htmlspecialchars($this->formId, ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * 取得表單 action URL。
     */
    private function getFormAction(): string
    {
        if ($this->payment instanceof Content) {
            return $this->payment->getApiUrl();
        }

        // 預設測試環境
        return 'https://ccore.newebpay.com/MPG/mpg_gateway';
    }

    /**
     * 輸出表單。
     */
    public function render(): void
    {
        echo $this->build();
    }

    /**
     * 轉換為字串。
     */
    public function __toString(): string
    {
        return $this->build();
    }
}
