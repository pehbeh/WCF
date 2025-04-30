<?php

namespace wcf\system\form\option;

use wcf\data\DatabaseObjectList;
use wcf\system\form\builder\field\AbstractFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\UrlFormField;
use wcf\system\form\option\formatter\UrlFormatter;
use wcf\system\WCF;

/**
 * Implementation of a form field for URLs.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class UrlFormOption extends AbstractFormOption
{
    #[\Override]
    public function getId(): string
    {
        return 'url';
    }

    #[\Override]
    public function getFormField(string $id, array $configurationData = []): UrlFormField
    {
        return UrlFormField::create($id);
    }

    #[\Override]
    public function getFormatter(): UrlFormatter
    {
        return new UrlFormatter();
    }

    #[\Override]
    public function getFilterFormField(string $id, array $configurationData = []): AbstractFormField
    {
        return TextFormField::create($id);
    }

    #[\Override]
    public function applyFilter(DatabaseObjectList $list, string $columnName, mixed $value): void
    {
        $list->getConditionBuilder()->add("{$columnName} LIKE ?", ['%' . WCF::getDB()->escapeLikeValue($value) . '%']);
    }
}
