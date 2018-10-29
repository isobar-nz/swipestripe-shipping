<?php
declare(strict_types=1);

namespace SwipeStripe\Shipping;

use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;

/**
 * Class ShippingPermissions
 * @package SwipeStripe\Shipping
 */
final class ShippingPermissions implements PermissionProvider
{
    const EDIT_SHIPPING = self::class . '.EDIT_SHIPPING';

    /**
     * @inheritDoc
     */
    public function providePermissions(): array
    {
        $category = _t(Permission::class . '.SWIPESTRIPE_SHIPPING_CATEGORY', 'SwipeStripe Shipping');

        return [
            self::EDIT_SHIPPING => [
                'name'     => _t(self::EDIT_SHIPPING, 'Edit shipping costs'),
                'category' => $category,
                'help'     => _t(self::EDIT_SHIPPING . '_HELP', 'Edit shipping costs in the CMS.'),
            ],
        ];
    }
}
