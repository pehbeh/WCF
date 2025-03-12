<?php

namespace wcf\system\database\table\column;

/**
 * Represents a `varchar` database table column with length `255` and whose values cannot be null.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   5.2
 */
final class NotNullVarchar255DatabaseTableColumn
{
    public static function create(string $name): VarcharDatabaseTableColumn
    {
        return VarcharDatabaseTableColumn::create($name)
            ->notNull()
            ->length(255);
    }

    private function __construct() {}
}
