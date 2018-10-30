<?php
declare(strict_types=1);

namespace SwipeStripe\Shipping\Tests\Order;

use SilverStripe\Dev\SapphireTest;
use SwipeStripe\Order\Order;
use SwipeStripe\Shipping\Order\OrderExtension;
use SwipeStripe\Shipping\Order\ShippingAddOn;
use SwipeStripe\Shipping\Tests\NeedsSupportedCurrencies;

/**
 * Class OrderExtensionTest
 * @package SwipeStripe\Shipping\Tests\Order
 */
class OrderExtensionTest extends SapphireTest
{
    use NeedsSupportedCurrencies;

    /**
     * @var bool
     */
    protected $usesDatabase = true;

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
    public function testGetShippingAddOn()
    {
        /** @var Order|OrderExtension $order */
        $order = Order::singleton()->createCart();

        $this->assertNull($order->OrderAddOns()->find('ClassName', ShippingAddOn::class));
        $this->assertInstanceOf(ShippingAddOn::class, $order->getShippingAddOn());
    }

    /**
     *
     */
    public function testGetShippingAddOnExisting()
    {
        /** @var Order|OrderExtension $order */
        $order = Order::singleton()->createCart();

        $addOn = ShippingAddOn::create();
        $addOn->OrderID = $order->ID;
        $addOn->write();

        $getAddOn = $order->getShippingAddOn();
        $this->assertInstanceOf(ShippingAddOn::class, $getAddOn);
        $this->assertSame($addOn->ID, $getAddOn->ID);
    }
}
