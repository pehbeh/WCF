<?php

namespace wcf\system\form\option;

use wcf\data\DatabaseObjectList;
use wcf\system\form\builder\field\AbstractFormField;
use wcf\system\form\option\formatter\DefaultFormatter;
use wcf\system\form\option\formatter\DefaultPlainTextFormatter;
use wcf\system\form\option\formatter\IFormOptionFormatter;
use wcf\system\WCF;

/**
 * Provides abstract implementations for form option types.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
abstract class AbstractFormOption implements IFormOption
{
    #[\Override]
    public function getConfigurationFormFields(): array
    {
        return ['required'];
    }

    #[\Override]
    public function getTitle(): string
    {
        return WCF::getLanguage()->get('wcf.form.option.' . $this->getId());
    }

    #[\Override]
    public function getFormatter(): IFormOptionFormatter
    {
        return new DefaultFormatter();
    }

    #[\Override]
    public function getPlainTextFormatter(): IFormOptionFormatter
    {
        return new DefaultPlainTextFormatter();
    }

    #[\Override]
    public function getFilterFormField(string $id, array $configurationData = []): AbstractFormField
    {
        return $this->getFormField($id, $configurationData);
    }

    #[\Override]
    public function applyFilter(DatabaseObjectList $list, string $columnName, mixed $value): void
    {
        $list->getConditionBuilder()->add("{$columnName} = ?", [$value]);
    }
}
