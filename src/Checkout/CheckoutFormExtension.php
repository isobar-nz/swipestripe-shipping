<?php
declare(strict_types=1);

namespace SwipeStripe\Shipping\Checkout;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormAction;
use SwipeStripe\Order\Checkout\CheckoutFormInterface;
use SwipeStripe\Order\Order;
use SwipeStripe\Shipping\Order\OrderExtension;
use SwipeStripe\Shipping\ShippingRegion;
use SwipeStripe\Shipping\ShippingService;

/**
 * Class CheckoutFormExtension
 * @package SwipeStripe\Shipping\Checkout
 * @property CheckoutFormInterface|CheckoutFormExtension $owner
 */
class CheckoutFormExtension extends Extension
{
    const SHIPPING_ADDRESS_FIELD = 'ShippingAddress';
    const SHIPPING_REGION_FIELD = 'ShippingRegion';
    const SHIPPING_SERVICE_FIELD = 'ShippingService';

    /**
     * @param FieldList $fields
     */
    public function updateFields(FieldList $fields): void
    {
        /** @var Order|OrderExtension $cart */
        $cart = $this->owner->getCart();
        $fields->insertAfter('BillingAddress', $cart->ShippingAddress->scaffoldFormField(
            _t(self::class . '.SHIPPING_ADDRESS_TITLE', 'Shipping Address')));

        $shippingAddOn = $cart->getShippingAddOn();
        $fields->insertAfter(static::SHIPPING_ADDRESS_FIELD,
            DropdownField::create(static::SHIPPING_REGION_FIELD,
                _t(self::class . '.SHIPPING_REGION_TITLE', 'Shipping Region'), ShippingRegion::get(),
                $shippingAddOn->ShippingRegionID));
        $fields->insertAfter(static::SHIPPING_REGION_FIELD,
            DropdownField::create(static::SHIPPING_SERVICE_FIELD,
                _t(self::class . '.SHIPPING_SERVICE_TITLE', 'Shipping Service'), ShippingService::get(),
                $shippingAddOn->ShippingZone()->ShippingServiceID));
    }

    /**
     * @param FieldList $actions
     */
    public function updateActions(FieldList $actions): void
    {
        $actions->unshift(FormAction::create('UpdateShipping',
            _t(self::class . '.UPDATE_SHIPPING_ACTION', 'Update Shipping Costs')));
    }
}
