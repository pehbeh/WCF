<?php

namespace wcf\data;

/**
 * Default interface for DatabaseObject-related actions.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface IDatabaseObjectAction
{
    /**
     * Executes the previously chosen action.
     *
     * @return mixed|mixed[]
     */
    public function executeAction();

    /**
     * Validates action-related parameters.
     *
     * @return void
     */
    public function validateAction();

    /**
     * Returns active action name.
     *
     * @return string
     */
    public function getActionName();

    /**
     * Returns DatabaseObject-related object ids.
     *
     * @return int[]
     */
    public function getObjectIDs();

    /**
     * Returns action-related parameters.
     *
     * @return mixed[]
     */
    public function getParameters();

    /**
     * Returns results returned by active action.
     *
     * @return array{
     *  actionName: string,
     *  objectIDs: int[],
     *  returnValues: mixed|mixed[]
     * }
     */
    public function getReturnValues();
}
