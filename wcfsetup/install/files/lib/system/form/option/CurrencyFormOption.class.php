<?php

namespace wcf\system\form\option;

use wcf\system\form\builder\field\AbstractFormField;
use wcf\system\form\builder\field\CurrencyFormField;
use wcf\system\form\option\formatter\CurrencyFormatter;
use wcf\system\form\option\formatter\IFormOptionFormatter;

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
    public function getFormField(string $id, array $configuration = []): AbstractFormField
    {
        $formField = CurrencyFormField::create($id);
        if (isset($configuration['currency'])) {
            $formField->currency($configuration['currency']);
        }
        if (isset($configuration['minFloatValue'])) {
            $formField->minimum($configuration['minFloatValue']);
        }
        if (isset($configuration['maxFloatValue'])) {
            $formField->maximum($configuration['maxFloatValue']);
        }

        return $formField;
    }

    #[\Override]
    public function getConfigurationFormFields(): array
    {
        return ['currency', 'minFloatValue', 'maxFloatValue', 'required'];
    }

    #[\Override]
    public function getFormatter(): IFormOptionFormatter
    {
        return new CurrencyFormatter();
    }

    #[\Override]
    public function getPlainTextFormatter(): IFormOptionFormatter
    {
        return $this->getFormatter();
    }
}
