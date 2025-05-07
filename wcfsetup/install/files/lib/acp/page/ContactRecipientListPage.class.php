<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\admin\ContactRecipientGridView;

/**
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 *
 * @extends AbstractGridViewPage<ContactRecipientGridView>
 */
final class ContactRecipientListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.contact.recipients';

    /**
     * @inheritDoc
     */
    public $neededModules = ['MODULE_CONTACT_FORM'];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.contact.canManageContactForm'];

    #[\Override]
    protected function createGridView(): ContactRecipientGridView
    {
        return new ContactRecipientGridView();
    }
}
