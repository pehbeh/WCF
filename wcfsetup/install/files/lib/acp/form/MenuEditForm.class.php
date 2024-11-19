<?php

namespace wcf\acp\form;

use CuyZ\Valinor\Mapper\MappingError;
use wcf\data\menu\Menu;
use wcf\form\AbstractFormBuilderForm;
use wcf\http\Helper;
use wcf\system\exception\IllegalLinkException;
use wcf\system\form\builder\field\TitleFormField;

/**
 * Shows the menu edit form.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   3.0
 */
class MenuEditForm extends MenuAddForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.cms.menu.list';

    /**
     * @inheritDoc
     */
    public $formAction = 'edit';

    /**
     * @inheritDoc
     */
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
            $this->formObject = new Menu($queryParameters['id']);

            if (!$this->formObject->getObjectID()) {
                throw new IllegalLinkException();
            }
        } catch (MappingError) {
            throw new IllegalLinkException();
        }
    }

    #[\Override]
    protected function createForm()
    {
        if ($this->formObject->isMainMenu()) {
            AbstractFormBuilderForm::createForm();

            $this->form->appendChildren([
                TitleFormField::create()
                    ->required()
                    ->i18n()
                    ->languageItemPattern('wcf.menu.(com.woltlab.wcf.genericMenu\d+|[\w\.]+)'),
            ]);
        } else {
            parent::createForm();
        }
    }
}
