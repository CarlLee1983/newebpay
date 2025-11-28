<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests\Unit\Parameter;

use CarlLee\NewebPay\Parameter\PaymentType;
use CarlLee\NewebPay\Tests\TestCase;

/**
 * 支付類型測試。
 */
class PaymentTypeTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertEquals('CREDIT', PaymentType::Credit->value);
        $this->assertEquals('CREDITAE', PaymentType::CreditAE->value);
        $this->assertEquals('WEBATM', PaymentType::WebAtm->value);
        $this->assertEquals('VACC', PaymentType::Vacc->value);
        $this->assertEquals('CVS', PaymentType::Cvs->value);
        $this->assertEquals('BARCODE', PaymentType::Barcode->value);
        $this->assertEquals('LINEPAY', PaymentType::LinePay->value);
        $this->assertEquals('ESUNWALLET', PaymentType::EsunWallet->value);
        $this->assertEquals('TAIWANPAY', PaymentType::TaiwanPay->value);
        $this->assertEquals('BITOPAY', PaymentType::BitoPay->value);
        $this->assertEquals('CVSCOM', PaymentType::Cvscom->value);
    }

    public function testValues(): void
    {
        $values = PaymentType::values();

        $this->assertIsArray($values);
        $this->assertContains('CREDIT', $values);
        $this->assertContains('VACC', $values);
        $this->assertContains('LINEPAY', $values);
    }

    public function testIsCredit(): void
    {
        $this->assertTrue(PaymentType::Credit->isCredit());
        $this->assertTrue(PaymentType::CreditAE->isCredit());
        $this->assertFalse(PaymentType::Vacc->isCredit());
    }

    public function testIsAtm(): void
    {
        $this->assertTrue(PaymentType::WebAtm->isAtm());
        $this->assertTrue(PaymentType::Vacc->isAtm());
        $this->assertFalse(PaymentType::Credit->isAtm());
    }

    public function testIsCvs(): void
    {
        $this->assertTrue(PaymentType::Cvs->isCvs());
        $this->assertTrue(PaymentType::Barcode->isCvs());
        $this->assertTrue(PaymentType::Cvscom->isCvs());
        $this->assertFalse(PaymentType::Credit->isCvs());
    }

    public function testIsEWallet(): void
    {
        $this->assertTrue(PaymentType::LinePay->isEWallet());
        $this->assertTrue(PaymentType::EsunWallet->isEWallet());
        $this->assertTrue(PaymentType::TaiwanPay->isEWallet());
        $this->assertTrue(PaymentType::BitoPay->isEWallet());
        $this->assertFalse(PaymentType::Credit->isEWallet());
    }

    public function testFromString(): void
    {
        $this->assertEquals(PaymentType::Credit, PaymentType::fromString('CREDIT'));
        $this->assertEquals(PaymentType::LinePay, PaymentType::fromString('LINEPAY'));
        $this->assertNull(PaymentType::fromString('INVALID'));
    }

    public function testTryFrom(): void
    {
        $this->assertEquals(PaymentType::Credit, PaymentType::tryFrom('CREDIT'));
        $this->assertNull(PaymentType::tryFrom('INVALID'));
    }
}
