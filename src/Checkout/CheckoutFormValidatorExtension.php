<?php
declare(strict_types=1);

namespace SwipeStripe\Shipping\Checkout;

use SilverStripe\Core\Extension;
use SwipeStripe\Order\Checkout\CheckoutForm;
use SwipeStripe\Order\Checkout\CheckoutFormValidator;
use SwipeStripe\Shipping\ShippingRegion;
use SwipeStripe\Shipping\ShippingService;
use SwipeStripe\Shipping\ShippingZone;

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
            $this->owner->addRequiredField(CheckoutFormExtension::SHIPPING_ADDRESS_FIELD . 'Street');
            $this->owner->addRequiredField(CheckoutFormExtension::SHIPPING_ADDRESS_FIELD . 'City');
            $this->owner->addRequiredField(CheckoutFormExtension::SHIPPING_ADDRESS_FIELD . 'PostCode');
            $this->owner->addRequiredField(CheckoutFormExtension::SHIPPING_ADDRESS_FIELD . 'Country');
        }

        $this->owner->addRequiredField(CheckoutFormExtension::SHIPPING_REGION_FIELD);
        $this->owner->addRequiredField(CheckoutFormExtension::SHIPPING_SERVICE_FIELD);
    }

    /**
     * @param CheckoutForm $form
     * @param array $data
     */
    public function validate(CheckoutForm $form, array $data): void
    {
        $shippingRegionId = intval($data[CheckoutFormExtension::SHIPPING_REGION_FIELD]);
        $shippingServiceId = intval($data[CheckoutFormExtension::SHIPPING_SERVICE_FIELD]);

        if (ShippingZone::getForRegionAndService($shippingRegionId, $shippingServiceId) === null) {
            $this->owner->validationError(CheckoutFormExtension::SHIPPING_SERVICE_FIELD,
                _t(self::class . '.SHIPPING_SERVICE_UNAVAILABLE', 'Sorry, {service} is not available in {region}.', [
                    'region'  => ShippingRegion::get_by_id($shippingRegionId)->Title,
                    'service' => ShippingService::get_by_id($shippingServiceId)->Title,
                ]));
        }
    }
}
