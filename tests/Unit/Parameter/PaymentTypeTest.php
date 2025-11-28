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
    public function testConstants(): void
    {
        $this->assertEquals('CREDIT', PaymentType::CREDIT);
        $this->assertEquals('CREDITAE', PaymentType::CREDITAE);
        $this->assertEquals('WEBATM', PaymentType::WEBATM);
        $this->assertEquals('VACC', PaymentType::VACC);
        $this->assertEquals('CVS', PaymentType::CVS);
        $this->assertEquals('BARCODE', PaymentType::BARCODE);
        $this->assertEquals('LINEPAY', PaymentType::LINEPAY);
        $this->assertEquals('ESUNWALLET', PaymentType::ESUNWALLET);
        $this->assertEquals('TAIWANPAY', PaymentType::TAIWANPAY);
        $this->assertEquals('BITOPAY', PaymentType::BITOPAY);
        $this->assertEquals('CVSCOM', PaymentType::CVSCOM);
    }

    public function testAll(): void
    {
        $all = PaymentType::all();

        $this->assertIsArray($all);
        $this->assertContains('CREDIT', $all);
        $this->assertContains('VACC', $all);
        $this->assertContains('LINEPAY', $all);
    }

    public function testIsCredit(): void
    {
        $this->assertTrue(PaymentType::isCredit('CREDIT'));
        $this->assertTrue(PaymentType::isCredit('CREDITAE'));
        $this->assertFalse(PaymentType::isCredit('VACC'));
    }

    public function testIsAtm(): void
    {
        $this->assertTrue(PaymentType::isAtm('WEBATM'));
        $this->assertTrue(PaymentType::isAtm('VACC'));
        $this->assertFalse(PaymentType::isAtm('CREDIT'));
    }

    public function testIsCvs(): void
    {
        $this->assertTrue(PaymentType::isCvs('CVS'));
        $this->assertTrue(PaymentType::isCvs('BARCODE'));
        $this->assertTrue(PaymentType::isCvs('CVSCOM'));
        $this->assertFalse(PaymentType::isCvs('CREDIT'));
    }

    public function testIsEWallet(): void
    {
        $this->assertTrue(PaymentType::isEWallet('LINEPAY'));
        $this->assertTrue(PaymentType::isEWallet('ESUNWALLET'));
        $this->assertTrue(PaymentType::isEWallet('TAIWANPAY'));
        $this->assertTrue(PaymentType::isEWallet('BITOPAY'));
        $this->assertFalse(PaymentType::isEWallet('CREDIT'));
    }
}
