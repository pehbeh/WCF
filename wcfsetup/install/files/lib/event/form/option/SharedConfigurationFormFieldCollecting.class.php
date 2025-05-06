<?php

namespace wcf\event\form\option;

use wcf\event\IPsr14Event;
use wcf\system\form\builder\field\IFormField;

/**
 * Requests the collection of shared configuration form fields used for form options.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class SharedConfigurationFormFieldCollecting implements IPsr14Event
{
    /**
     * @var array<string, IFormField>
     */
    private array $formFields = [];

    /**
     * Registers a new shared configuration form field.
     */
    public function register(IFormField $formField): void
    {
        $this->formFields[$formField->getId()] = $formField;
    }

    /**
     * @return array<string, IFormField>
     */
    public function getFormFields(): array
    {
        return $this->formFields;
    }
}
