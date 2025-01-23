<?php

namespace wcf\system\form\builder\container\wysiwyg;

use wcf\system\form\builder\container\IFormContainer;
use wcf\system\style\FontAwesomeIcon;

/**
 * Represents a container that is a tab of a wysiwyg tab menu.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
interface IWysiwygTabFormContainer extends IFormContainer
{
    /**
     * Gets the icon associated with the tab.
     */
    public function getIcon(): ?FontAwesomeIcon;

    /**
     * Gets the name associated with the tab.
     */
    public function getName(): string;
}
