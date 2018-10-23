<?php
declare(strict_types=1);

namespace SwipeStripe\Shipping\Order;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SwipeStripe\Order\Order;
use SwipeStripe\ORM\FieldType\DBAddress;

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
     */
    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeFieldFromTab('Root.Main', 'ShippingAddress');
        $fields->insertAfter('BillingAddress', $this->owner->ShippingAddress->scaffoldFormField());
    }
}
