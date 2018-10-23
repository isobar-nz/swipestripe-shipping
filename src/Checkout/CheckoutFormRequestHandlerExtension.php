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
     * @param array $data
     * @param CheckoutForm|CheckoutFormExtension $form
     */
    public function beforeInitPayment(array $data, CheckoutForm $form): void
    {
        // Order specific shipping address handled by saveInto()
        if ($form->shippingAddressSameAsBillingAddress()) {
            /** @var Order|OrderExtension $cart */
            $cart = $form->getCart();
            $cart->ShippingAddress->setValue($cart->BillingAddress);
        }
    }
}
