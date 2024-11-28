<?php

namespace wcf\acp\form;

use CuyZ\Valinor\Mapper\MappingError;
use wcf\data\menu\item\MenuItem;
use wcf\data\menu\Menu;
use wcf\form\AbstractFormBuilderForm;
use wcf\http\Helper;
use wcf\system\exception\IllegalLinkException;

/**
 * Shows the menu item edit form.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   3.0
 */
class MenuItemEditForm extends MenuItemAddForm
{
    /**
     * @inheritDoc
     */
    public $formAction = 'edit';

    #[\Override]
    public function readParameters()
    {
        AbstractFormBuilderForm::readParameters();

        try {
            $queryParameters = Helper::mapQueryParameters(
                $_GET,
                <<<'EOT'
                    array {
                        id: positive-int
                    }
                    EOT
            );
            $this->formObject = new MenuItem($queryParameters['id']);

            if (!$this->formObject->getObjectID()) {
                throw new IllegalLinkException();
            }
        } catch (MappingError) {
            throw new IllegalLinkException();
        }

        $this->menuID = $this->formObject->menuID;
        $this->menu = new Menu($this->menuID);
    }

    #[\Override]
    protected function setFormAction()
    {
        AbstractFormBuilderForm::setFormAction();
    }
}
