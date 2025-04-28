<?php

namespace wcf\acp\form;

use wcf\data\contact\option\ContactOption;
use wcf\data\contact\option\ContactOptionAction;
use wcf\data\contact\option\ContactOptionList;
use wcf\data\IStorableObject;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\form\builder\data\processor\CustomFormDataProcessor;
use wcf\system\form\builder\field\BooleanFormField;
use wcf\system\form\builder\field\dependency\ValueFormFieldDependency;
use wcf\system\form\builder\field\IFormField;
use wcf\system\form\builder\field\MultilineTextFormField;
use wcf\system\form\builder\field\SelectFormField;
use wcf\system\form\builder\field\ShowOrderFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\IFormDocument;
use wcf\system\form\option\FormOptionHandler;
use wcf\system\form\option\SharedConfigurationFormFields;
use wcf\util\JSON;

/**
 * Shows the contact option add form.
 *
 * @author      Alexander Ebert
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       3.1
 *
 * @extends AbstractFormBuilderForm<ContactOption>
 */
class ContactOptionAddForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.contact.settings';

    /**
     * @inheritDoc
     */
    public $neededModules = ['MODULE_CONTACT_FORM'];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.contact.canManageContactForm'];

    /**
     * @inheritDoc
     */
    public $objectActionClass = ContactOptionAction::class;

    #[\Override]
    protected function createForm()
    {
        parent::createForm();

        $this->form->appendChildren([
            TextFormField::create('optionTitle')
                ->label('wcf.global.name')
                ->maximumLength(255)
                ->i18n()
                ->languageItemPattern('wcf.contact.option\d+')
                ->required(),
            MultilineTextFormField::create('optionDescription')
                ->label('wcf.global.description')
                ->maximumLength(5000)
                ->rows(5)
                ->i18n()
                ->languageItemPattern('wcf.contact.optionDescription\d+'),
            ShowOrderFormField::create('showOrder')
                ->options($this->getContactOptions()),
            SelectFormField::create('optionType')
                ->label('wcf.acp.customOption.optionType')
                ->immutable($this->formAction != 'create')
                ->options($this->getAvailableOptionTypes())
                ->required(),
            ...$this->getSharedConfigurationFormFields(),
            BooleanFormField::create('required')
                ->label('wcf.acp.customOption.required'),
            BooleanFormField::create('isDisabled')
                ->label('wcf.acp.customOption.isDisabled'),
        ]);
    }

    #[\Override]
    public function finalizeForm()
    {
        parent::finalizeForm();

        $this->form->getDataHandler()->addProcessor(
            new CustomFormDataProcessor(
                'saveOptionProcessor',
                function (IFormDocument $document, array $parameters) {
                    $configurationData = [];

                    foreach ($this->getConfigurationFormFieldIds() as $parameter) {
                        if (isset($parameters['data'][$parameter])) {
                            $configurationData[$parameter] = $parameters['data'][$parameter];
                            unset($parameters['data'][$parameter]);
                        }
                    }

                    if ($configurationData !== []) {
                        $parameters['data']['configurationData'] = JSON::encode($configurationData);
                    }

                    return $parameters;
                },
                function (IFormDocument $document, array $data, IStorableObject $object) {
                    \assert($object instanceof ContactOption);

                    if ($object->configurationData) {
                        $data = \array_merge($data, JSON::decode($object->configurationData));
                    }

                    return $data;
                }
            )
        );
    }

    /**
     * @return array<int, string>
     */
    private function getContactOptions(): array
    {
        $optionList = new ContactOptionList();
        $optionList->sqlOrderBy = 'showOrder ASC';
        $optionList->readObjects();

        return \array_map(static fn($option) => $option->getTitle(), $optionList->getObjects());
    }

    /**
     * @return array<string, string>
     */
    private function getAvailableOptionTypes(): array
    {
        return \array_map(fn($option) => $option->getId(), FormOptionHandler::getInstance()->getOptions());
    }

    /**
     * @return IFormField[]
     */
    private function getSharedConfigurationFormFields(): array
    {
        $sharedConfigurationFormFields = new SharedConfigurationFormFields();
        $matrix = [];

        foreach (FormOptionHandler::getInstance()->getOptions() as $option) {
            foreach ($option->getConfigurationFormFields() as $formFieldId) {
                if (!isset($matrix[$formFieldId])) {
                    $matrix[$formFieldId] = [];
                }

                $matrix[$formFieldId][] = $option->getId();
            }
        }

        $formFields = [];

        foreach ($matrix as $formFieldId => $dependencies) {
            $formField = $sharedConfigurationFormFields->getFormField($formFieldId);
            $formField->addDependency(
                ValueFormFieldDependency::create($formFieldId . 'OptionTypeDependency')
                    ->fieldId('optionType')
                    ->values($dependencies)
            );
            $formFields[] = $formField;
        }

        return $formFields;
    }

    /**
     * @return string[]
     */
    private function getConfigurationFormFieldIds(): array
    {
        $ids = [];

        foreach (FormOptionHandler::getInstance()->getOptions() as $option) {
            foreach ($option->getConfigurationFormFields() as $formFieldId) {
                $ids[] = $formFieldId;
            }
        }

        return \array_unique($ids);
    }
}
