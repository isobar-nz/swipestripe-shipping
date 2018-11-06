<?php
declare(strict_types=1);

namespace SwipeStripe\Shipping\Order;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SwipeStripe\Address\DBAddress;
use SwipeStripe\Order\Order;

/**
 * Class OrderExtension
 * @package SwipeStripe\Shipping\Order
 * @property Order|OrderExtension $owner
 * @property DBAddress $ShippingAddress
 */
class OrderExtension extends DataExtension
{
    /**
     * @var array
     */
    private static $db = [
        'ShippingAddress' => 'Address',
    ];

    /**
     * @param FieldList $fields
     * @codeCoverageIgnore
     */
    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeFieldFromTab('Root.Main', 'ShippingAddress');
        $fields->insertAfter('BillingAddress', $this->owner->ShippingAddress->scaffoldFormField());
    }

    /**
     * @return ShippingAddOn
     */
    public function getShippingAddOn(): ShippingAddOn
    {
        $existing = $this->owner->OrderAddOns()->find('ClassName', ShippingAddOn::class);

        if ($existing !== null) {
            return $existing;
        }

        $new = ShippingAddOn::create();
        $new->OrderID = $this->owner->ID;

        return $new;
    }
}
