<?php

namespace wcf\system\html\output\node;

use wcf\system\html\node\IHtmlNode;

/**
 * Default interface for html output nodes.
 *
 * @author      Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       3.0
 */
interface IHtmlOutputNode extends IHtmlNode
{
    /**
     * Sets the desired output type.
     *
     * @param string $outputType desired output type
     * @return void
     */
    public function setOutputType($outputType);
}
