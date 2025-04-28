<?php

namespace wcf\system\form\option;

use wcf\system\form\builder\field\AbstractFormField;
use wcf\system\form\builder\field\CurrencyFormField;
use wcf\system\form\option\formatter\CurrencyFormatter;

/**
 * Implementation of a form field for currency values.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class CurrencyFormOption extends AbstractFormOption
{
    #[\Override]
    public function getId(): string
    {
        return 'currency';
    }

    #[\Override]
    public function getFormField(string $id, array $configurationData = []): AbstractFormField
    {
        $formField = CurrencyFormField::create($id);
        if (isset($configurationData['currency'])) {
            $formField->currency($configurationData['currency']);
        }
        if (isset($configurationData['minFloatValue'])) {
            $formField->minimum($configurationData['minFloatValue']);
        }
        if (isset($configurationData['maxFloatValue'])) {
            $formField->maximum($configurationData['maxFloatValue']);
        }

        return $formField;
    }

    #[\Override]
    public function getConfigurationFormFields(): array
    {
        return ['currency', 'minFloatValue', 'maxFloatValue'];
    }

    #[\Override]
    public function getFormatter(): CurrencyFormatter
    {
        return new CurrencyFormatter();
    }

    #[\Override]
    public function getPlainTextFormatter(): CurrencyFormatter
    {
        return $this->getFormatter();
    }
}
