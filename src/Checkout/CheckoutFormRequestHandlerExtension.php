<?php
declare(strict_types=1);

namespace SwipeStripe\Shipping\Checkout;

use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\Extension;
use SilverStripe\ORM\ValidationException;
use SilverStripe\ORM\ValidationResult;
use SwipeStripe\Order\Checkout\CheckoutFormInterface;
use SwipeStripe\Order\Checkout\CheckoutFormRequestHandler;
use SwipeStripe\Order\Order;
use SwipeStripe\Shipping\Order\OrderExtension;
use SwipeStripe\Shipping\ShippingRegion;
use SwipeStripe\Shipping\ShippingService;
use SwipeStripe\Shipping\ShippingZone;

/**
 * Class CheckoutFormRequestHandlerExtension
 * @package SwipeStripe\Shipping\Checkout
 * @property CheckoutFormRequestHandler|CheckoutFormRequestHandlerExtension $owner
 */
class CheckoutFormRequestHandlerExtension extends Extension
{
    /**
     * @param CheckoutFormInterface|CheckoutFormExtension $form
     * @param array $data
     */
    public function beforeConfirmCheckout(CheckoutFormInterface $form, array $data): void
    {
        $this->updateShippingAddOn($form->getCart(), intval($data[CheckoutFormExtension::SHIPPING_REGION_FIELD]),
            intval($data[CheckoutFormExtension::SHIPPING_SERVICE_FIELD]));
    }

    /**
     * @param Order|OrderExtension $cart
     * @param int $regionId
     * @param int $serviceId
     */
    protected function updateShippingAddOn(Order $cart, int $regionId, int $serviceId): void
    {
        $shippingZone = ShippingZone::getForRegionAndService($regionId, $serviceId);

        if ($shippingZone === null) {
            throw new ValidationException(ValidationResult::create()
                ->addFieldError(CheckoutFormExtension::SHIPPING_SERVICE_FIELD,
                    _t(CheckoutFormValidatorExtension::class . '.SHIPPING_SERVICE_UNAVAILABLE',
                        'Sorry, {service} is not available in {region}.',
                        [
                            'region'  => ShippingRegion::get_by_id($regionId)->Title,
                            'service' => ShippingService::get_by_id($serviceId)->Title,
                        ])));
        }

        $cart->getShippingAddOn()
            ->updateWithZone($shippingZone, $regionId)
            ->write();
    }

    /**
     * @param array $data
     * @param CheckoutFormInterface $form
     * @return \SilverStripe\Control\HTTPResponse
     */
    public function UpdateShipping(array $data, CheckoutFormInterface $form): HTTPResponse
    {
        if (!$form->getCart()->IsMutable()) {
            // If the cart was locked due to trying to pay, then checkout was clicked again
            // This stops being able to create multiple active checkouts on one order
            $original = $form->getCart();
            $clone = $original->duplicate();
            $clone->Unlock();

            if ($original->ID === $this->owner->ActiveCart->ID) {
                $this->owner->setActiveCart($clone);
            }

            $form->setCart($clone);
        }

        $this->updateShippingAddOn($form->getCart(), intval($data[CheckoutFormExtension::SHIPPING_REGION_FIELD]),
            intval($data[CheckoutFormExtension::SHIPPING_SERVICE_FIELD]));

        return $this->owner->redirectBack();
    }
}
