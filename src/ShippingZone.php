<?php
declare(strict_types=1);

namespace SwipeStripe\Shipping;

use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ManyManyList;
use SilverStripe\Versioned\Versioned;
use SwipeStripe\Price\DBPrice;

/**
 * Class ShippingZone
 * @package SwipeStripe\Shipping
 * @property bool $IsDefault
 * @property DBPrice $Price
 * @property int $ShippingServiceID
 * @method ShippingService ShippingService()
 * @method ManyManyList|ShippingRegion[] ShippingRegions()
 * @mixin Versioned
 */
class ShippingZone extends DataObject
{
    /**
     * @var string
     */
    private static $table_name = 'SwipeStripe_Shipping_Zone';

    /**
     * @var array
     */
    private static $db = [
        'IsDefault' => 'Boolean',
        'Price'     => 'Price',
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'ShippingService' => ShippingService::class,
    ];

    /**
     * @var array
     */
    private static $many_many = [
        'ShippingRegions' => ShippingRegion::class,
    ];

    /**
     * @var array
     */
    private static $extensions = [
        Versioned::class => Versioned::class,
    ];

    /**
     * @var array
     */
    private static $owns = [
        'ShippingRegions',
        'ShippingService',
    ];

    /**
     * @var array
     */
    private static $searchable_fields = [
        'ShippingService.Title',
        'ShippingRegions.Title',
        'IsDefault',
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'ShippingService.Title' => 'Shipping Service',
        'Price.Value'           => 'Price',
        'ShippingRegions.Count' => 'Regions',
        'IsDefault.Nice'        => 'Default',
    ];

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->ShippingService()->Title;
    }

    /**
     * @inheritDoc
     */
    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();

        if (!DataObject::get_one(static::class)) {
            // Ensure shipping regions and default shipping service exist
            ShippingRegion::singleton()->requireDefaultRecords();
            ShippingService::singleton()->requireDefaultRecords();

            $defaultZone = static::create();
            $defaultZone->IsDefault = true;
            $defaultZone->ShippingServiceID = DataObject::get_one(ShippingService::class)->ID;
            $defaultZone->write();

            $defaultZone->ShippingRegions()->addMany(ShippingRegion::get());
        }
    }
}
