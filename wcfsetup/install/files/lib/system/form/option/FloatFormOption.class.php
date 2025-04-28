<?php

namespace wcf\system\form\option;

use wcf\system\form\builder\field\AbstractFormField;
use wcf\system\form\builder\field\FloatFormField;
use wcf\system\form\option\formatter\FloatFormatter;

/**
 * Implementation of a form field for float values.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class FloatFormOption extends AbstractFormOption
{
    #[\Override]
    public function getId(): string
    {
        return 'float';
    }

    #[\Override]
    public function getFormField(string $id, array $configurationData = []): AbstractFormField
    {
        $formField = FloatFormField::create($id);
        if (isset($configurationData['minValue'])) {
            $formField->minimum($configurationData['minValue']);
        }
        if (isset($configurationData['maxValue'])) {
            $formField->maximum($configurationData['maxValue']);
        }

        return $formField;
    }

    #[\Override]
    public function getConfigurationFormFields(): array
    {
        return ['minFloatValue', 'maxFloatValue'];
    }

    #[\Override]
    public function getFormatter(): FloatFormatter
    {
        return new FloatFormatter();
    }

    #[\Override]
    public function getPlainTextFormatter(): FloatFormatter
    {
        return $this->getFormatter();
    }
}
