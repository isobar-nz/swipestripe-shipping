<?php
declare(strict_types=1);

namespace SwipeStripe\Shipping\Checkout;

use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\Extension;
use SwipeStripe\Order\Checkout\CheckoutForm;
use SwipeStripe\Order\Checkout\CheckoutFormRequestHandler;
use SwipeStripe\Order\Order;
use SwipeStripe\Shipping\Order\OrderExtension;
use SwipeStripe\Shipping\ShippingZone;

/**
 * Class CheckoutFormRequestHandlerExtension
 * @package SwipeStripe\Shipping\Checkout
 * @property CheckoutFormRequestHandler|CheckoutFormRequestHandlerExtension $owner
 */
class CheckoutFormRequestHandlerExtension extends Extension
{
    /**
     * @param CheckoutForm|CheckoutFormExtension $form
     * @param array $data
     */
    public function beforeInitPayment(CheckoutForm $form, array $data): void
    {
        /** @var Order|OrderExtension $cart */
        $cart = $form->getCart();

        // Order specific shipping address handled by saveInto()
        if ($form->shippingAddressSameAsBillingAddress()) {
            $cart->ShippingAddress->setValue($cart->BillingAddress);
        }

        $this->updateShippingAddOn($cart, intval($data[CheckoutFormExtension::SHIPPING_REGION_FIELD]),
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
        $cart->getShippingAddOn()
            ->updateWithZone($shippingZone, $regionId)
            ->write();
    }

    /**
     * @param array $data
     * @param CheckoutForm $form
     * @return \SilverStripe\Control\HTTPResponse
     */
    public function UpdateShipping(array $data, CheckoutForm $form): HTTPResponse
    {
        $this->updateShippingAddOn($form->getCart(), intval($data[CheckoutFormExtension::SHIPPING_REGION_FIELD]),
            intval($data[CheckoutFormExtension::SHIPPING_SERVICE_FIELD]));

        return $this->owner->redirectBack();
    }
}
