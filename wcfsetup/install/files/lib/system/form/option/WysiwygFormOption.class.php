<?php

namespace wcf\system\form\option;

use wcf\data\DatabaseObjectList;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\wysiwyg\WysiwygFormField;
use wcf\system\form\option\formatter\WysiwygFormatter;
use wcf\system\form\option\formatter\WysiwygPlainTextFormatter;
use wcf\system\WCF;

/**
 * Implementation of a form option using the WYSIWYG editor.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class WysiwygFormOption extends AbstractFormOption
{
    #[\Override]
    public function getId(): string
    {
        return 'wysiwyg';
    }

    #[\Override]
    public function getFilterFormField(string $id, array $configurationData = []): TextFormField
    {
        return TextFormField::create($id);
    }

    #[\Override]
    public function getFormField(string $id, array $configurationData = []): WysiwygFormField
    {
        return WysiwygFormField::create($id)
            ->objectType('com.woltlab.wcf.genericFormOption');
    }

    #[\Override]
    public function getConfigurationFormFields(): array
    {
        return ['required'];
    }

    #[\Override]
    public function applyFilter(DatabaseObjectList $list, string $columnName, mixed $value): void
    {
        $list->getConditionBuilder()->add("{$columnName} LIKE ?", ['%' . WCF::getDB()->escapeLikeValue($value) . '%']);
    }

    #[\Override]
    public function getFormatter(): WysiwygFormatter
    {
        return new WysiwygFormatter();
    }

    #[\Override]
    public function getPlainTextFormatter(): WysiwygPlainTextFormatter
    {
        return new WysiwygPlainTextFormatter();
    }
}
