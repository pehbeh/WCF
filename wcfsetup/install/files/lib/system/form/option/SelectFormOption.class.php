<?php

namespace wcf\system\form\option;

use wcf\system\form\builder\field\AbstractFormField;
use wcf\system\form\builder\field\SelectFormField;
use wcf\system\form\option\formatter\SelectFormatter;
use wcf\system\WCF;
use wcf\util\JSON;

/**
 * Implementation of a form option for selecting a single value.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class SelectFormOption extends AbstractFormOption
{
    #[\Override]
    public function getId(): string
    {
        return 'select';
    }

    #[\Override]
    public function getFormField(string $id, array $configurationData = []): AbstractFormField
    {
        $formField = SelectFormField::create($id);

        if (isset($configurationData['selectOptions'])) {
            $selectOptions = [];
            foreach (JSON::decode($configurationData['selectOptions']) as $selectOption) {
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
    public function getFormatter(): SelectFormatter
    {
        return new SelectFormatter();
    }

    #[\Override]
    public function getPlainTextFormatter(): SelectFormatter
    {
        return new SelectFormatter(false);
    }
}
