<?php

namespace wcf\system\form\option;

use wcf\system\form\builder\field\AbstractFormField;
use wcf\system\form\builder\field\TextFormField;

/**
 * Implementation of a form field for single-line text values.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class TextFormOption extends AbstractFormOption
{
    #[\Override]
    public function getId(): string
    {
        return 'text';
    }

    #[\Override]
    public function getFormField(string $id, array $configurationData = []): AbstractFormField
    {
        $formField = TextFormField::create($id);
        if (!empty($configurationData['maxLength'])) {
            $formField->maximumLength($configurationData['maxLength']);
        }
        if (isset($configurationData['defaultTextValue'])) {
            $formField->value($configurationData['defaultTextValue']);
        }

        return $formField;
    }

    #[\Override]
    public function getConfigurationFormFields(): array
    {
        return ['maxLength', 'defaultTextValue'];
    }
}
