<?php

namespace wcf\system\database\table\column;

use wcf\system\database\editor\DatabaseEditor;

/**
 * Represents a column of a database table.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   5.2
 * @phpstan-import-type ExistingColumnDefinition from DatabaseEditor
 */
interface IDatabaseTableColumn
{
    /**
     * Marks the column to be dropped and returns the column.
     */
    public function drop(): static;

    /**
     * Returns the data used by `DatabaseEditor` to add the column to a table.
     *
     * @return array{
     *  notNull: 0|1,
     *  type: string,
     *  default?: ?mixed,
     *  autoIncrement?: 0|1,
     *  key?: string,
     *  decimals?: int,
     *  values?: string,
     *  length?: ?int,
     * }
     */
    public function getData(): array;

    /**
     * Returns the name of the column.
     */
    public function getName(): string;

    /**
     * Returns the new name of the column or `null` if the column's name is unchanged.
     *
     * @since 5.4
     */
    public function getNewName(): ?string;

    /**
     * Returns the type of the column.
     */
    public function getType(): string;

    /**
     * Returns `true` if the values of the column cannot be `null`.
     */
    public function isNotNull(): bool;

    /**
     * Sets the name of the column and returns the column.
     */
    public function name(string $name): static;

    /**
     * Sets if the values of the column cannot be `null`.
     */
    public function notNull(bool $notNull = true): static;

    /**
     * Sets the new name of the column and returns the column.
     *
     * @since 5.4
     */
    public function renameTo(string $newName): static;

    /**
     * Returns `true` if the column will be dropped.
     */
    public function willBeDropped(): bool;

    /**
     * Returns a `DatabaseTableColumn` object with the given name.
     */
    public static function create(string $name): static;

    /**
     * Returns a `DatabaseTableColumn` object with the given name and data.
     *
     * @param ExistingColumnDefinition $data data returned by `DatabaseEditor::getColumns()`
     */
    public static function createFromData(string $name, array $data): static;
}
