<?php

namespace wcf\system\form\option;

use wcf\system\form\builder\field\DateFormField;
use wcf\system\form\option\formatter\DateFormatter;

/**
 * Implementation of a form field for date values.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class DateFormOption extends AbstractFormOption
{
    #[\Override]
    public function getId(): string
    {
        return 'date';
    }

    #[\Override]
    public function getFormField(string $id, array $configuration = []): DateFormField
    {
        $formField = DateFormField::create($id)
            ->saveValueFormat('Y-m-d');

        return $formField;
    }

    #[\Override]
    public function getFormatter(): DateFormatter
    {
        return new DateFormatter();
    }

    #[\Override]
    public function getPlainTextFormatter(): DateFormatter
    {
        return $this->getFormatter();
    }
}
