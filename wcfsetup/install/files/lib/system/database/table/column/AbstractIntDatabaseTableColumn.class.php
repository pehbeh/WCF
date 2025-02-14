<?php

namespace wcf\system\database\table\column;

/**
 * Abstract implementation of an integer database table column.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2020 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   5.2
 */
abstract class AbstractIntDatabaseTableColumn extends AbstractDatabaseTableColumn implements
    IAutoIncrementDatabaseTableColumn,
    IDefaultValueDatabaseTableColumn,
    ILengthDatabaseTableColumn
{
    use TAutoIncrementDatabaseTableColumn;
    use TDefaultValueDatabaseTableColumn;
    use TLengthDatabaseTableColumn;

    /**
     * @inheritDoc
     */
    public function getMinimumLength(): int
    {
        return 1;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public static function createFromData(string $name, array $data): static
    {
        $length = $data['length'] ?? null;

        // Unset `length` so that `parent::createFromData()` does not validate the length
        // which is done below.
        $data['length'] = null;

        $column = parent::createFromData($name, $data);

        if ($length) {
            try {
                $column->length($length);
            } catch (\InvalidArgumentException $e) {
                // Ignore exceptions due to the length being to large.
                // Such cases can happen when columns were created using the SQL PIP
                // where the length (which is just a display length for integer column
                // types) is not validated. To update tables with such columns,
                // exceptions related to the maximum length must be ignored.
                if ($length > $column->getMaximumLength()) {
                    $column->length = $length;
                } else {
                    throw $e;
                }
            }
        }

        return $column;
    }
}
