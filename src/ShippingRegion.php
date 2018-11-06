<?php
declare(strict_types=1);

namespace SwipeStripe\Shipping;

use SilverStripe\Forms\FieldList;
use SilverStripe\i18n\i18n;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ManyManyList;
use SilverStripe\Versioned\Versioned;

/**
 * Class ShippingRegion
 * @package SwipeStripe\Shipping
 * @property int $Sort
 * @method ManyManyList|ShippingZone[] ShippingZones()
 * @mixin Versioned
 */
class ShippingRegion extends DataObject
{
    /**
     * @var string
     */
    private static $table_name = 'SwipeStripe_Shipping_Region';

    /**
     * @var array
     */
    private static $db = [
        'Title' => 'Varchar',
        'Sort'  => 'Int',
    ];

    /**
     * @var array
     */
    private static $default_sort = [
        'Sort' => 'ASC',
        'ID'   => 'ASC',
    ];

    /**
     * @var array
     */
    private static $belongs_many_many = [
        'ShippingZones' => ShippingZone::class,
    ];

    /**
     * @var array
     */
    private static $extensions = [
        Versioned::class => Versioned::class,
    ];

    /**
     * @inheritDoc
     */
    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();

        if (!DataObject::get_one(static::class)) {
            foreach (i18n::getData()->getCountries() as $country) {
                $region = static::create();
                $region->Title = $country;
                $region->write();
            }
        }
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName('Sort');
        });

        return parent::getCMSFields();
    }
}
