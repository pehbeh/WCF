<?php

namespace wcf\system\form\option;

use wcf\system\form\builder\field\AbstractFormField;
use wcf\system\form\builder\field\IntegerFormField;
use wcf\system\form\option\formatter\IntegerFormatter;

/**
 * Implementation of a form field for integer values.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class IntegerFormOption extends AbstractFormOption
{
    #[\Override]
    public function getId(): string
    {
        return 'integer';
    }

    #[\Override]
    public function getFormField(string $id, array $configurationData = []): AbstractFormField
    {
        $formField = IntegerFormField::create($id);
        if (isset($configurationData['minIntegerValue'])) {
            $formField->minimum($configurationData['minIntegerValue']);
        }
        if (isset($configurationData['maxIntegerValue'])) {
            $formField->maximum($configurationData['maxIntegerValue']);
        }

        return $formField;
    }

    #[\Override]
    public function getConfigurationFormFields(): array
    {
        return ['minIntegerValue', 'maxIntegerValue'];
    }

    #[\Override]
    public function getFormatter(): IntegerFormatter
    {
        return new IntegerFormatter();
    }

    #[\Override]
    public function getPlainTextFormatter(): IntegerFormatter
    {
        return $this->getFormatter();
    }
}
