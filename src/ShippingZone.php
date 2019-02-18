<?php
declare(strict_types=1);

namespace SwipeStripe\Shipping;

use Money\Money;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ManyManyList;
use SilverStripe\Versioned\Versioned;
use SwipeStripe\Order\Order;
use SwipeStripe\Price\DBPrice;

/**
 * Class ShippingZone
 * @package SwipeStripe\Shipping
 * @property DBPrice $Price
 * @property DBPrice $FreeOver
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
        'Title'    => 'Varchar',
        'Price'    => 'Price',
        'FreeOver' => 'Price',
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
        'Title',
        'ShippingService.Title',
        'ShippingRegions.Title',
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'Title'                 => 'Title',
        'ShippingService.Title' => 'Shipping Service',
        'Price.Value'           => 'Price',
        'ShippingRegions.Count' => 'Regions',
    ];

    /**
     * @param int $regionID
     * @param int $serviceID
     * @return null|ShippingZone
     */
    public static function getForRegionAndService(int $regionID, int $serviceID): ?self
    {
        return static::get()->filter([
            'ShippingRegions.ID' => $regionID,
            'ShippingServiceID'  => $serviceID,
        ])->first();
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->getField('Title') ?: $this->ShippingService()->Title;
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
            $defaultZone->ShippingServiceID = DataObject::get_one(ShippingService::class)->ID;
            $defaultZone->write();

            $defaultZone->ShippingRegions()->addMany(ShippingRegion::get());
        }
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->dataFieldByName('FreeOver')->setDescription('Set to a positive amount to make ' .
                'shipping free over a certain amount. Set as zero or negative to disable free shipping over $x for ' .
                'this zone.');
        });

        return parent::getCMSFields();
    }

    /**
     * @param Order $order
     * @return DBPrice
     */
    public function PriceForOrder(Order $order): DBPrice
    {
        $orderSubTotal = $order->SubTotal()->getMoney();
        $zoneFreeOver = $this->FreeOver->getMoney();

        $price = $zoneFreeOver->isPositive() && $orderSubTotal->greaterThanOrEqual($zoneFreeOver)
            ? DBPrice::create_field(DBPrice::INJECTOR_SPEC,
                new Money(0, $this->Price->getMoney()->getCurrency()))
            : $this->Price;


        $this->extend('updatePriceForOrder', $price, $order);

        return $price;
    }
}
