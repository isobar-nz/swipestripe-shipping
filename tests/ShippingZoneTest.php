<?php
declare(strict_types=1);

namespace SwipeStripe\Shipping\Tests;

use Money\Money;
use SilverStripe\Dev\SapphireTest;
use SwipeStripe\Order\Order;
use SwipeStripe\Shipping\ShippingRegion;
use SwipeStripe\Shipping\ShippingService;
use SwipeStripe\Shipping\ShippingZone;
use SwipeStripe\Shipping\Tests\Fixtures\Fixtures;
use SwipeStripe\Shipping\Tests\Fixtures\PublishesFixtures;

/**
 * Class ShippingZoneTest
 * @package SwipeStripe\Shipping\Tests
 */
class ShippingZoneTest extends SapphireTest
{
    use NeedsSupportedCurrencies;
    use PublishesFixtures;

    /**
     * @var array
     */
    protected static $fixture_file = [
        Fixtures::PRODUCTS,
        Fixtures::SHIPPING,
    ];

    /**
     * @var array
     */
    protected static $extra_dataobjects = [
        TestProduct::class,
    ];

    /**
     * @var bool
     */
    protected $usesDatabase = true;

    /**
     * @var ShippingService
     */
    private $standardService, $expeditedService;

    /**
     * @var ShippingZone
     */
    private $standardZone, $expeditedZone;

    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        static::setupSupportedCurrencies();
    }

    /**
     *
     */
    public function testGetExistingZoneForServiceRegion(): void
    {
        $standardWest = ShippingZone::getForRegionAndService(
            $this->idFromFixture(ShippingRegion::class, 'west'),
            $this->standardService->ID
        );

        $this->assertNotNull($standardWest);
        $this->assertSame($this->standardZone->ID, $standardWest->ID);

        $expeditedNorth = ShippingZone::getForRegionAndService(
            $this->idFromFixture(ShippingRegion::class, 'north'),
            $this->expeditedService->ID
        );

        $this->assertNotNull($expeditedNorth);
        $this->assertSame($this->expeditedZone->ID, $expeditedNorth->ID);
    }

    /**
     *
     */
    public function testGetUnavailableZoneForServiceRegion()
    {
        $this->assertNull(ShippingZone::getForRegionAndService(
            $this->idFromFixture(ShippingRegion::class, 'west'),
            $this->expeditedService->ID
        ));
    }

    /**
     *
     */
    public function testPriceForOrder()
    {
        $order = Order::singleton()->createCart();

        /** @var TestProduct $product */
        $product = $this->objFromFixture(TestProduct::class, 'product');
        $order->addItem($product, 3);

        $this->assertTrue($this->standardZone->Price->getMoney()->equals(
            $this->standardZone->PriceForOrder($order)->getMoney()
        ));

        $this->assertTrue($this->expeditedZone->Price->getMoney()->equals(
            $this->expeditedZone->PriceForOrder($order)->getMoney()
        ));
    }

    /**
     *
     */
    public function testPriceForOrderFreeOver()
    {
        $order = Order::singleton()->createCart();

        /** @var TestProduct $product */
        $product = $this->objFromFixture(TestProduct::class, 'product');
        // Take the order up to $50, making standard shipping free
        $order->addItem($product, 5);

        $free = new Money(0, $this->standardZone->Price->getMoney()->getCurrency());
        $this->assertTrue($free->equals(
            $this->standardZone->PriceForOrder($order)->getMoney()
        ));

        $this->assertTrue($this->expeditedZone->Price->getMoney()->equals(
            $this->expeditedZone->PriceForOrder($order)->getMoney()
        ));
    }

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->registerPublishingBlueprint(ShippingZone::class);
        $this->registerPublishingBlueprint(TestProduct::class);

        parent::setUp();

        $this->standardService = $this->objFromFixture(ShippingService::class, 'standard');
        $this->expeditedService = $this->objFromFixture(ShippingService::class, 'expedited');

        $this->standardZone = $this->objFromFixture(ShippingZone::class, 'standard');
        $this->expeditedZone = $this->objFromFixture(ShippingZone::class, 'expedited');
    }
}
