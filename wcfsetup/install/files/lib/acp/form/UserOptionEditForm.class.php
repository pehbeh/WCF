<?php

namespace wcf\acp\form;

use CuyZ\Valinor\Mapper\MappingError;
use wcf\data\user\option\UserOption;
use wcf\form\AbstractFormBuilderForm;
use wcf\http\Helper;
use wcf\system\exception\IllegalLinkException;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\language\I18nHandler;

/**
 * Shows the user option edit form.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class UserOptionEditForm extends UserOptionAddForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.user.option.list';

    /**
     * @inheritDoc
     */
    public $formAction = 'edit';

    #[\Override]
    public function readParameters()
    {
        parent::readParameters();

        try {
            $queryParameters = Helper::mapQueryParameters(
                $_GET,
                <<<'EOT'
                    array {
                        id: positive-int
                    }
                    EOT
            );
        } catch (MappingError) {
            throw new IllegalLinkException();
        }

        $this->formObject = new UserOption($queryParameters['id']);

        if (!$this->formObject->getObjectID()) {
            throw new IllegalLinkException();
        }
    }

    #[\Override]
    public function createForm()
    {
        parent::createForm();

        if ($this->formObject->optionName === 'aboutMe') {
            $optionType = $this->form->getNodeById('optionType');
            \assert($optionType instanceof SingleSelectionFormField);

            $optionType->options([
                ...$optionType->getOptions(),
                'aboutMe' => 'aboutMe',
            ]);
        }
    }

    #[\Override]
    public function saved()
    {
        I18nHandler::getInstance()->save(
            'optionName',
            'wcf.user.option.' . $this->formObject->optionName,
            'wcf.user.option'
        );
        I18nHandler::getInstance()->save(
            'optionDescription',
            'wcf.user.option.' . $this->formObject->optionName . '.description',
            'wcf.user.option'
        );

        AbstractFormBuilderForm::saved();
    }
}
