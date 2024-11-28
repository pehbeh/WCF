<?php

namespace wcf\acp\form;

use wcf\data\label\group\LabelGroupList;
use wcf\data\label\Label;
use wcf\data\label\LabelAction;
use wcf\data\label\LabelList;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\BadgeColorFormField;
use wcf\system\form\builder\field\IFormField;
use wcf\system\form\builder\field\IntegerFormField;
use wcf\system\form\builder\field\SelectFormField;
use wcf\system\form\builder\field\ShowOrderFormField;
use wcf\system\form\builder\field\TextFormField;

/**
 * Shows the label add form.
 *
 * @author      Olaf Braun, Alexander Ebert
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @property Label $formObject
 */
class LabelAddForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.label.add';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.content.label.canManageLabel'];

    /**
     * @inheritDoc
     */
    public $objectEditLinkController = LabelEditForm::class;

    /**
     * @inheritDoc
     */
    public $objectActionClass = LabelAction::class;

    #[\Override]
    protected function createForm()
    {
        parent::createForm();

        $this->form->appendChildren([
            FormContainer::create('section')
                ->appendChildren([
                    SelectFormField::create('groupID')
                        ->label('wcf.acp.label.group')
                        ->options(new LabelGroupList())
                        ->immutable($this->formAction !== 'create')
                        ->description('wcf.acp.label.group.permanentSelection')
                        ->required(),
                    TextFormField::create('label')
                        ->i18n()
                        ->required()
                        ->languageItemPattern('wcf.acp.label.label\d+'),
                    $this->getShowOrderField(),
                    BadgeColorFormField::create('cssClassName')
                        ->label('wcf.acp.label.cssClassName')
                        ->required()
                        ->textReferenceNodeId('label')
                ])
        ]);
    }

    protected function getShowOrderField(): IFormField
    {
        if ($this->formAction === 'create') {
            return IntegerFormField::create('showOrder')
                ->addFieldClass('tiny')
                ->value(0)
                ->label('wcf.form.field.showOrder');
        } else {
            return ShowOrderFormField::create()
                ->options(function () {
                    $labelList = new LabelList();
                    $labelList->getConditionBuilder()->add('groupID = ?', [$this->formObject->groupID]);
                    $labelList->getConditionBuilder()->add('labelID <> ?', [$this->formObject->labelID]);
                    $labelList->sqlOrderBy = 'showOrder';
                    $labelList->readObjects();

                    return $labelList->getObjects();
                });
        }
    }
}
