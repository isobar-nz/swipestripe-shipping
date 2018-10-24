<?php
declare(strict_types=1);

namespace SwipeStripe\Shipping\Checkout;

use SilverStripe\Core\Extension;
use SwipeStripe\Order\Checkout\CheckoutForm;
use SwipeStripe\Order\Checkout\CheckoutFormRequestHandler;
use SwipeStripe\Order\Order;
use SwipeStripe\Shipping\Order\OrderExtension;

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
        // Order specific shipping address handled by saveInto()
        if ($form->shippingAddressSameAsBillingAddress()) {
            /** @var Order|OrderExtension $cart */
            $cart = $form->getCart();
            $cart->ShippingAddress->setValue($cart->BillingAddress);
        }
    }
}
