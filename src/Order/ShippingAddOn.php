<?php
declare(strict_types=1);

namespace SwipeStripe\Shipping\Order;

use SwipeStripe\Order\OrderAddOn;
use SwipeStripe\Shipping\ShippingRegion;
use SwipeStripe\Shipping\ShippingService;
use SwipeStripe\Shipping\ShippingZone;

/**
 * Class ShippingAddOn
 * @package SwipeStripe\Shipping\Order
 * @property int $ShippingRegionID
 * @property int $ShippingServiceID
 * @method ShippingService ShippingService()
 * @method ShippingRegion ShippingRegion()
 */
class ShippingAddOn extends OrderAddOn
{
    /**
     * @var string
     */
    private static $table_name = 'SwipeStripe_Shipping_AddOn';

    /**
     * @var array
     */
    private static $has_one = [
        'ShippingRegion'  => ShippingRegion::class,
        'ShippingService' => ShippingService::class,
    ];

    /**
     * @param ShippingZone $shippingZone
     * @param int $regionId
     * @return $this
     */
    public function updateWithZone(ShippingZone $shippingZone, int $regionId): self
    {
        $this->Title = _t(self::class . '.ORDER_ENTRY', 'Shipping - {service}', [
            'service' => $shippingZone->ShippingService()->Title,
        ]);
        $this->ShippingRegionID = $regionId;
        $this->ShippingServiceID = $shippingZone->ShippingServiceID;
        $this->Amount->setValue($shippingZone->PriceForOrder($this->Order()));

        return $this;
    }
}
