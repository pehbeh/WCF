<?php

namespace wcf\system\condition;

use wcf\data\condition\Condition;
use wcf\data\IDatabaseObjectProcessor;

/**
 * Every concrete condition implementation needs to implement this interface.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface ICondition extends IDatabaseObjectProcessor
{
    /**
     * Returns the data saved with the condition used to check if the condition
     * is fulfilled. If null is returned, there is no condition to be created.
     *
     * @return ?mixed[]
     */
    public function getData();

    /**
     * Returns the output for setting up the condition.
     *
     * @return string
     */
    public function getHTML();

    /**
     * Reads the form parameters of the condition.
     *
     * @return void
     */
    public function readFormParameters();

    /**
     * Resets the internally stored condition data.
     *
     * @return void
     */
    public function reset();

    /**
     * Extracts all needed data from the given condition to pre-fill the output
     * for editing the given condition.
     *
     * @return void
     */
    public function setData(Condition $condition);

    /**
     * Validates the read condition data.
     *
     * @return void
     */
    public function validate();
}
