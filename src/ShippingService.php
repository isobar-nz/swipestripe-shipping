<?php
declare(strict_types=1);

namespace SwipeStripe\Shipping;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\HasManyList;
use SilverStripe\Versioned\Versioned;

/**
 * Class ShippingService
 * @package SwipeStripe\Shipping
 * @property string $Description
 * @property int $Sort
 * @method HasManyList|ShippingZone[] ShippingZones()
 * @mixin Versioned
 */
class ShippingService extends DataObject
{
    /**
     * @var string
     */
    private static $table_name = 'SwipeStripe_Shipping_Service';

    /**
     * @var array
     */
    private static $db = [
        'Title'       => 'Varchar',
        'Description' => 'Varchar',
        'Sort'        => 'Int',
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
    private static $has_many = [
        'ShippingZones' => ShippingZone::class,
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
    private static $default_records = [
        'default' => [
            'Title'       => 'Default',
            'Description' => 'Default shipping service',
        ],
    ];

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName('Sort');

            $zones = $fields->dataFieldByName('ShippingZones');
            if ($zones instanceof GridField) {
                $zones->getConfig()->removeComponentsByType(GridFieldAddExistingAutocompleter::class);
            }
        });

        return parent::getCMSFields();
    }
}
