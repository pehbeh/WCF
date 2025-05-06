<?php

namespace wcf\system\form\option;

use wcf\system\form\builder\field\AbstractFormField;
use wcf\system\form\builder\field\RadioButtonFormField;

/**
 * Implementation of a form option for selecting a single value using radio buttons.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class RadioButtonFormOption extends SelectFormOption
{
    use TSelectOptionsFormOption;

    #[\Override]
    public function getId(): string
    {
        return 'radioButton';
    }

    #[\Override]
    public function getFormField(string $id, array $configuration = []): AbstractFormField
    {
        $formField = RadioButtonFormField::create($id);
        $this->setSelectOptions($formField, $configuration);

        return $formField;
    }
}
