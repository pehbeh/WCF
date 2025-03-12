<?php

namespace wcf\system\setup;

/**
 * Logs files and checks their overwriting rights.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface IFileHandler
{
    /**
     * Checks the overwriting rights of the given files.
     *
     * @param list<string> $files
     * @return void
     */
    public function checkFiles(array $files);

    /**
     * Logs the given list of files.
     *
     * @param list<string> $files
     * @return void
     */
    public function logFiles(array $files);
}
