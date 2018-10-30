<?php
declare(strict_types=1);

namespace SwipeStripe\Shipping\Tests\Order;

use SilverStripe\Dev\SapphireTest;
use SwipeStripe\Order\Order;
use SwipeStripe\Shipping\Order\ShippingAddOn;
use SwipeStripe\Shipping\ShippingRegion;
use SwipeStripe\Shipping\ShippingZone;
use SwipeStripe\Shipping\Tests\Fixtures\Fixtures;
use SwipeStripe\Shipping\Tests\Fixtures\PublishesFixtures;
use SwipeStripe\Shipping\Tests\NeedsSupportedCurrencies;

/**
 * Class ShippingAddOnTest
 * @package SwipeStripe\Shipping\Tests\Order
 */
class ShippingAddOnTest extends SapphireTest
{
    use NeedsSupportedCurrencies;
    use PublishesFixtures;

    /**
     * @var array
     */
    protected static $fixture_file = [
        Fixtures::SHIPPING,
    ];

    /**
     * @var bool
     */
    protected $usesDatabase = true;

    /**
     * @var ShippingZone
     */
    private $standardZone;

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
    public function testUpdateWithZone()
    {
        $northId = $this->idFromFixture(ShippingRegion::class, 'north');

        $order = Order::singleton()->createCart();

        $addOn = ShippingAddOn::create();
        $addOn->OrderID = $order->ID;
        $addOn->updateWithZone($this->standardZone, $northId);

        $this->assertSame($northId, $addOn->ShippingRegion()->ID);
        $this->assertSame($this->standardZone->ID, $addOn->ShippingZone()->ID);

        $this->assertTrue($addOn->Amount->getMoney()->equals(
            $this->standardZone->PriceForOrder($order)->getMoney()
        ));
    }

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->registerPublishingBlueprint(ShippingZone::class);

        parent::setUp();

        $this->standardZone = $this->objFromFixture(ShippingZone::class, 'standard');
    }
}
