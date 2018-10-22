<?php
declare(strict_types=1);

namespace SwipeStripe\Shipping\Checkout;

use SilverStripe\Core\Extension;
use SwipeStripe\Order\Checkout\CheckoutForm;
use SwipeStripe\Order\Checkout\CheckoutFormValidator;

/**
 * Class CheckoutFormValidatorExtension
 * @package SwipeStripe\Shipping\Checkout
 * @property CheckoutFormValidator|CheckoutFormValidatorExtension $owner
 */
class CheckoutFormValidatorExtension extends Extension
{
    /**
     * @param CheckoutForm|CheckoutFormExtension $form
     */
    public function beforeRequiredFields(CheckoutForm $form): void
    {
        if (!$form->shippingAddressSameAsBillingAddress()) {
            $this->owner->addRequiredField('ShippingAddressStreet');
            $this->owner->addRequiredField('ShippingAddressCity');
            $this->owner->addRequiredField('ShippingAddressPostCode');
            $this->owner->addRequiredField('ShippingAddressCountry');
        }
    }
}
