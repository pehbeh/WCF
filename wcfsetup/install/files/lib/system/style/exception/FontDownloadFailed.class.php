<?php

namespace wcf\system\style\exception;

/**
 * Indicates that the font download failed.
 *
 * @author  Tim Duesterhus
 * @copyright   2001-2020 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   5.3
 */
class FontDownloadFailed extends \Exception
{
    private string $reason = '';

    /**
     * @param string $message
     * @param string $reason
     */
    public function __construct($message, $reason = '', ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);

        $this->reason = $reason;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }
}
