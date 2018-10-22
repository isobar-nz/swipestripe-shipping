<?php
declare(strict_types=1);

namespace SwipeStripe\Shipping\Checkout;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SwipeStripe\Order\Checkout\CheckoutForm;
use SwipeStripe\Order\Order;
use SwipeStripe\Shipping\Order\OrderExtension;

/**
 * Class CheckoutFormExtension
 * @package SwipeStripe\Shipping\Checkout
 * @property CheckoutForm|CheckoutFormExtension $owner
 */
class CheckoutFormExtension extends Extension
{
    const SHIPPING_ADDRESS_COPY_FIELD = 'ShippingAddressSame';
    const SHIPPING_ADDRESS_FIELD = 'ShippingAddress';

    /**
     * @param FieldList $fields
     */
    public function updateFields(FieldList $fields): void
    {
        $fields->insertAfter('BillingAddress', CheckboxField::create(static::SHIPPING_ADDRESS_COPY_FIELD,
            _t(self::class . '.SHIPPING_ADDRESS_SAME', 'Shipping address same as billing address'), true));

        /** @var Order|OrderExtension $cart */
        $cart = $this->owner->getCart();
        $fields->insertAfter(static::SHIPPING_ADDRESS_COPY_FIELD, $cart->ShippingAddress->scaffoldFormField());
    }

    /**
     * @return bool
     */
    public function shippingAddressSameAsBillingAddress(): bool
    {
        return boolval($this->owner->Fields()->dataFieldByName(static::SHIPPING_ADDRESS_COPY_FIELD)->dataValue());
    }
}
