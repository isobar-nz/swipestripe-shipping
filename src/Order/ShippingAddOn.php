<?php
declare(strict_types=1);

namespace SwipeStripe\Shipping\Order;

use SwipeStripe\Order\OrderAddOn;
use SwipeStripe\Price\DBPrice;
use SwipeStripe\Shipping\ShippingRegion;
use SwipeStripe\Shipping\ShippingZone;

/**
 * Class ShippingAddOn
 * @package SwipeStripe\Shipping\Order
 * @property int $ShippingRegionID
 * @property int $ShippingZoneID
 * @method ShippingRegion ShippingRegion()
 * @method ShippingZone ShippingZone()
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
        'ShippingRegion' => ShippingRegion::class,
        'ShippingZone'   => ShippingZone::class,
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
        $this->ShippingZoneID = $shippingZone->ID;

        return $this;
    }

    /**
     * @return DBPrice
     */
    public function getAmount(): DBPrice
    {
        return $this->ShippingZone()->PriceForOrder($this->Order());
    }
}
