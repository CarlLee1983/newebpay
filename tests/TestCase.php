<?php

declare(strict_types=1);

namespace CarlLee\NewebPay\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * 測試基礎類別。
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * 測試用特店編號。
     */
    protected const TEST_MERCHANT_ID = 'MS12345678';

    /**
     * 測試用 HashKey。
     */
    protected const TEST_HASH_KEY = '12345678901234567890123456789012';

    /**
     * 測試用 HashIV。
     */
    protected const TEST_HASH_IV = '1234567890123456';
}
