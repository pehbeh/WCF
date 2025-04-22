<?php

namespace wcf\system\form\option;

use wcf\event\form\option\SharedConfigurationFormFieldCollecting;
use wcf\system\event\EventHandler;
use wcf\system\form\builder\field\IFormField;
use wcf\system\form\builder\field\IntegerFormField;
use wcf\system\form\builder\field\SelectOptionsFormField;
use wcf\system\SingletonFactory;

/**
 * Provides the available shared configuration form fields.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class SharedConfigurationFormFields extends SingletonFactory
{
    /**
     * @var array<string, IFormField>
     */
    private array $formFields;

    #[\Override]
    protected function init()
    {
        $this->formFields = \array_merge($this->getDefaultFormFields(), $this->getEventFormFields());
    }

    /**
     * @return array<string, IFormField>
     */
    private function getDefaultFormFields(): array
    {
        return [
            'maxLength' => IntegerFormField::create('maxLength')
                ->label('wcf.acp.customOption.maxLength'),
            'selectOptions' => SelectOptionsFormField::create('selectOptions')
                ->label('wcf.acp.customOption.selectOptions')
                ->required()
        ];
    }

    /**
     * @return array<string, IFormField>
     */
    private function getEventFormFields(): array
    {
        $event = new SharedConfigurationFormFieldCollecting();
        EventHandler::getInstance()->fire($event);

        return $event->getFormFields();
    }

    /**
     * @return array<string, IFormField>
     */
    public function getFormFields(): array
    {
        return $this->formFields;
    }

    public function getFormField(string $identifier): ?IFormField
    {
        return $this->formFields[$identifier] ?? null;
    }
}
