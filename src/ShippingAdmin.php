<?php
declare(strict_types=1);

namespace SwipeStripe\Shipping;

use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Forms\GridField\GridField;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * Class ShippingAdmin
 * @package SwipeStripe\Shipping
 */
class ShippingAdmin extends ModelAdmin
{
    /**
     * @var string
     */
    private static $menu_title = 'Shipping';

    /**
     * @var string
     */
    private static $url_segment = 'swipestripe/shipping';

    /**
     * @var array
     */
    private static $required_permission_codes = [
        ShippingPermissions::EDIT_SHIPPING,
    ];

    /**
     * @var array
     */
    private static $managed_models = [
        ShippingZone::class,
        ShippingService::class,
        ShippingRegion::class,
    ];

    /**
     * @inheritDoc
     */
    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm($id, $fields);

        if ($this->modelClass === ShippingRegion::class || $this->modelClass === ShippingService::class) {
            $field = $form->Fields()->dataFieldByName($this->sanitiseClassName($this->modelClass));

            if ($field instanceof GridField) {
                $field->getConfig()->addComponent(new GridFieldOrderableRows('Sort'));
            }
        }

        return $form;
    }
}
