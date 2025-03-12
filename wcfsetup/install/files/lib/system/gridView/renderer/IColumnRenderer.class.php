<?php

namespace wcf\system\gridView\renderer;

use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectDecorator;

/**
 * Represents a column renderer of a grid view.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 *
 * @template-covariant TDatabaseObject of DatabaseObject|DatabaseObjectDecorator
 */
interface IColumnRenderer
{
    /**
     * Renders the content of a column with the given value.
     *
     * @template TRow of TDatabaseObject
     * @param TRow $row
     */
    public function render(mixed $value, DatabaseObject $row): string;

    /**
     * Returns the css classes of a column.
     */
    public function getClasses(): string;

    /**
     * Is called after the grid view data has been loaded and allows additional data to be loaded or cached.
     *
     * @template TRow of TDatabaseObject
     * @param TRow $row
     */
    public function prepare(mixed $value, DatabaseObject $row): void;
}
