<?php

namespace wcf\system\form\option;

use wcf\system\form\builder\field\AbstractFormField;
use wcf\system\form\builder\field\DateFormField;
use wcf\system\form\option\formatter\DateFormatter;
use wcf\system\form\option\formatter\IFormOptionFormatter;

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
    public function getFormField(string $id, array $configuration = []): AbstractFormField
    {
        $formField = DateFormField::create($id)
            ->saveValueFormat('Y-m-d');

        return $formField;
    }

    #[\Override]
    public function getFormatter(): IFormOptionFormatter
    {
        return new DateFormatter();
    }

    #[\Override]
    public function getPlainTextFormatter(): IFormOptionFormatter
    {
        return $this->getFormatter();
    }
}
