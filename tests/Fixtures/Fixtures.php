<?php
declare(strict_types=1);

namespace SwipeStripe\Shipping\Tests\Fixtures;

/**
 * Class Fixtures
 * @package SwipeStripe\Shipping\Tests\Fixtures
 */
final class Fixtures
{
    const FIXTURE_BASE_PATH = __DIR__;

    const PRODUCTS = self::FIXTURE_BASE_PATH . '/TestProducts.yml';
    const SHIPPING = self::FIXTURE_BASE_PATH . '/Shipping.yml';

    /**
     * Fixtures constructor.
     */
    private function __construct()
    {
    }
}
