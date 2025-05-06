<?php

namespace wcf\system\form\option;

use wcf\system\form\builder\field\DateFormField;
use wcf\system\form\builder\field\MultipleSelectionFormField;
use wcf\system\form\option\formatter\DateFormatter;
use wcf\system\form\option\formatter\MultipleSelectionFormatter;
use wcf\system\WCF;
use wcf\util\JSON;

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
    #[\Override]
    public function getId(): string
    {
        return 'checkboxes';
    }

    #[\Override]
    public function getFormField(string $id, array $configuration = []): MultipleSelectionFormField
    {
        $formField = MultipleSelectionFormField::create($id);

        if (isset($configuration['selectOptions'])) {
            $selectOptions = [];
            foreach (JSON::decode($configuration['selectOptions']) as $selectOption) {
                if (isset($selectOption['value'][0])) {
                    $value = $selectOption['value'][0];
                } else if (isset($selectOption['value'][WCF::getLanguage()->languageID])) {
                    $value = $selectOption['value'][WCF::getLanguage()->languageID];
                } else {
                    $value = reset($selectOption['value']);
                }

                $selectOptions[$selectOption['key']] = $value;
            }

            $formField->options($selectOptions);
        }

        return $formField;
    }

    #[\Override]
    public function getConfigurationFormFields(): array
    {
        return ['selectOptions', 'required'];
    }

    #[\Override]
    public function getFormatter(): MultipleSelectionFormatter
    {
        return new MultipleSelectionFormatter();
    }

    #[\Override]
    public function getPlainTextFormatter(): MultipleSelectionFormatter
    {
        return $this->getFormatter();
    }
}
