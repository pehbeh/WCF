<?php

namespace wcf\action;

use Psr\Http\Message\ResponseInterface;

/**
 * All action classes should implement this interface.
 * An action executes a user input without showing a result page or a form.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface IAction
{
    /**
     * Initializes this action.
     *
     * @return void|ResponseInterface
     */
    public function __run();

    /**
     * Reads the given parameters.
     *
     * @return void|ResponseInterface
     */
    public function readParameters();

    /**
     * Checks the modules of this action.
     *
     * @return void
     */
    public function checkModules();

    /**
     * Checks the permissions of this action.
     *
     * @return void
     */
    public function checkPermissions();

    /**
     * Executes this action.
     *
     * @return void|ResponseInterface
     */
    public function execute();
}
