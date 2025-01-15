<?php

namespace wcf\system\form\builder\container\wysiwyg;

use wcf\system\form\builder\container\TabFormContainer;

/**
 * Represents a container that is a tab of a wysiwyg tab menu.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class WysiwygTabFormContainer extends TabFormContainer
{
    protected ?string $icon = null;

    /**
     * Gets the icon associated with the tab.
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * Sets the icon associated with the tab.
     */
    public function setIcon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }
}
