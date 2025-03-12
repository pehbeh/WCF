<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\admin\LanguageGridView;

/**
 * Shows a list of all installed languages.
 *
 * @author      Olaf Brau, Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractGridViewPage<LanguageGridView>
 */
final class LanguageListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.language.list';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.language.canManageLanguage'];

    #[\Override]
    protected function createGridView(): LanguageGridView
    {
        return new LanguageGridView();
    }
}
