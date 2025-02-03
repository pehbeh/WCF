<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\admin\BoxGridView;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

/**
 * Shows a list of boxes.
 *
 * @author      Olaf Braun, Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   3.0
 *
 * @property    BoxGridView $gridView
 */
class BoxListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.cms.box.list';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.content.cms.canManageBox'];

    /**
     * display 'Add Box' dialog on load
     * @var int
     */
    public $showBoxAddDialog = 0;

    #[\Override]
    public function readParameters()
    {
        parent::readParameters();

        if (!empty($_REQUEST['showBoxAddDialog'])) {
            $this->showBoxAddDialog = 1;
        }
    }

    #[\Override]
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'availableLanguages' => LanguageFactory::getInstance()->getLanguages(),
            'showBoxAddDialog' => $this->showBoxAddDialog,
        ]);
    }

    #[\Override]
    protected function createGridViewController(): AbstractGridView
    {
        return new BoxGridView();
    }
}
