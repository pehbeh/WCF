<?php

namespace wcf\system\database\table\column;

/**
 * Provides default implementation of the methods of `IDefaultValueDatabaseTableColumn`.
 *
 * @author  Tim Duesterhus
 * @copyright   2001-2022 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   5.5
 */
trait TDefaultValueDatabaseTableColumn
{
    /**
     * default value of the database table column
     * @var mixed
     */
    protected $defaultValue;

    /**
     * Checks if the given default value is valid.
     *
     * @param mixed $defaultValue validated default value
     * @return void
     */
    protected function validateDefaultValue(mixed $defaultValue)
    {
        // does nothing
    }

    /**
     * Sets the default value of the column and returns the column.
     *
     * @return  $this
     */
    public function defaultValue(mixed $defaultValue): static
    {
        $this->validateDefaultValue($defaultValue);

        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * Returns the default value of the column.
     */
    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }
}
