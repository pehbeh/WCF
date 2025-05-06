<?php

namespace wcf\system\form\option;

use wcf\system\form\builder\field\AbstractFormField;
use wcf\system\form\builder\field\MultipleSelectionFormField;
use wcf\system\form\option\formatter\IFormOptionFormatter;
use wcf\system\form\option\formatter\MultipleSelectionFormatter;

/**
 * Implementation of a form field for selecting multiple values.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class CheckboxesFormOption extends AbstractFormOption
{
    use TSelectOptionsFormOption;

    #[\Override]
    public function getId(): string
    {
        return 'checkboxes';
    }

    #[\Override]
    public function getFormField(string $id, array $configuration = []): AbstractFormField
    {
        $formField = MultipleSelectionFormField::create($id);
        $this->setSelectOptions($formField, $configuration);

        return $formField;
    }

    #[\Override]
    public function getConfigurationFormFields(): array
    {
        return ['selectOptions', 'required'];
    }

    #[\Override]
    public function getFormatter(): IFormOptionFormatter
    {
        return new MultipleSelectionFormatter();
    }

    #[\Override]
    public function getPlainTextFormatter(): IFormOptionFormatter
    {
        return $this->getFormatter();
    }
}
