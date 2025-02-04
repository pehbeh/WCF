<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\admin\LanguageItemGridView;
use wcf\system\WCF;

/**
 * Shows a list of language items.
 *
 * @author      Olaf Braun, Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @property LanguageItemGridView $gridView
 */
class LanguageItemListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.language.item.list';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.language.canManageLanguage'];

    #[\Override]
    protected function createGridViewController(): AbstractGridView
    {
        return new LanguageItemGridView(WCF::getLanguage());
    }
}
