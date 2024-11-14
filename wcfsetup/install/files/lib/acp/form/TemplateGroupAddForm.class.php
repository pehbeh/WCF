<?php

namespace wcf\acp\form;

use wcf\data\template\group\TemplateGroup;
use wcf\data\template\group\TemplateGroupAction;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\form\builder\field\SelectFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\WCF;
use wcf\util\FileUtil;

/**
 * Shows the form for adding new template groups.
 *
 * @author      Olaf Braun, Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @property TemplateGroup $formObject
 */
class TemplateGroupAddForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.template.group.add';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.template.canManageTemplate'];

    /**
     * @inheritDoc
     */
    public $objectActionClass = TemplateGroupAction::class;

    /**
     * @inheritDoc
     */
    public $objectEditLinkController = TemplateGroupEditForm::class;

    #[\Override]
    protected function createForm()
    {
        parent::createForm();

        $availableTemplateGroups = TemplateGroup::getSelectList([-1], 1);

        $this->form->appendChildren([
            SelectFormField::create('parentTemplateGroupID')
                ->label('wcf.acp.template.group.parentTemplateGroup')
                ->options($availableTemplateGroups)
                ->available(\count($availableTemplateGroups) > 0),
            TextFormField::create('templateGroupName')
                ->label('wcf.global.name')
                ->required()
                ->addValidator(
                    new FormFieldValidator('templateNameValidator', function (TextFormField $formField) {
                        if ($formField->getValue() === $this->formObject?->templateGroupName) {
                            return;
                        }

                        $sql = "SELECT  COUNT(*)
                                FROM    wcf1_template_group
                                WHERE   templateGroupName = ?";
                        $statement = WCF::getDB()->prepare($sql);
                        $statement->execute([$formField->getValue()]);

                        if ($statement->fetchSingleColumn()) {
                            $formField->addValidationError(
                                new FormFieldValidationError(
                                    'notUnique',
                                    'wcf.acp.template.group.name.error.notUnique'
                                )
                            );
                        }
                    })
                ),
            TextFormField::create('templateGroupFolderName')
                ->label('wcf.acp.template.group.folderName')
                ->required()
                ->addValidator(
                    new FormFieldValidator('folderNameValidator', function (TextFormField $formField) {
                        $formField->value(FileUtil::addTrailingSlash($formField->getValue()));

                        if (!\preg_match('/^[a-z0-9_\- ]+\/$/i', $formField->getValue())) {
                            $formField->addValidationError(
                                new FormFieldValidationError(
                                    'invalid',
                                    'wcf.acp.template.group.folderName.error.invalid'
                                )
                            );
                        }
                    })
                )
                ->addValidator(
                    new FormFieldValidator('uniqueFolderNameValidator', function (TextFormField $formField) {
                        $formField->value(FileUtil::addTrailingSlash($formField->getValue()));

                        if ($formField->getValue() === $this->formObject?->templateGroupFolderName) {
                            return;
                        }

                        $sql = "SELECT  COUNT(*)
                                FROM    wcf1_template_group
                                WHERE   templateGroupFolderName = ?";
                        $statement = WCF::getDB()->prepare($sql);
                        $statement->execute([$formField->getValue()]);

                        if ($statement->fetchSingleColumn()) {
                            $formField->addValidationError(
                                new FormFieldValidationError(
                                    'notUnique',
                                    'wcf.acp.template.group.folderName.error.notUnique'
                                )
                            );
                        }
                    })
                ),
        ]);
    }
}
