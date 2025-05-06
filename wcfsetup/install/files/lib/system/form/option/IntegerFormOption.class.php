<?php

namespace wcf\system\form\option;

use wcf\system\form\builder\field\AbstractFormField;
use wcf\system\form\builder\field\IntegerFormField;
use wcf\system\form\option\formatter\IFormOptionFormatter;
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
    public function getFormField(string $id, array $configuration = []): AbstractFormField
    {
        $formField = IntegerFormField::create($id);
        if (isset($configuration['minIntegerValue'])) {
            $formField->minimum($configuration['minIntegerValue']);
        }
        if (isset($configuration['maxIntegerValue'])) {
            $formField->maximum($configuration['maxIntegerValue']);
        }

        return $formField;
    }

    #[\Override]
    public function getConfigurationFormFields(): array
    {
        return ['minIntegerValue', 'maxIntegerValue', 'required'];
    }

    #[\Override]
    public function getFormatter(): IFormOptionFormatter
    {
        return new IntegerFormatter();
    }

    #[\Override]
    public function getPlainTextFormatter(): IFormOptionFormatter
    {
        return $this->getFormatter();
    }
}
