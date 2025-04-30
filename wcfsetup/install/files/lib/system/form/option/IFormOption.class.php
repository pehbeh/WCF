<?php

namespace wcf\system\form\option;

use wcf\data\DatabaseObjectList;
use wcf\system\form\builder\field\AbstractFormField;
use wcf\system\form\option\formatter\IFormOptionFormatter;

/**
 * Represents a form option type.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
interface IFormOption
{
    public function getId(): string;

    public function getTitle(): string;

    /**
     * @param array<string, mixed> $configurationData
     */
    public function getFormField(string $id, array $configurationData = []): AbstractFormField;

    /**
     * @param array<string, mixed> $configurationData
     */
    public function getFilterFormField(string $id, array $configurationData = []): AbstractFormField;

    /**
     * @return string[]
     */
    public function getConfigurationFormFields(): array;

    public function getFormatter(): IFormOptionFormatter;

    public function getPlainTextFormatter(): IFormOptionFormatter;

    public function applyFilter(DatabaseObjectList $list, string $columnName, mixed $value): void;
}
