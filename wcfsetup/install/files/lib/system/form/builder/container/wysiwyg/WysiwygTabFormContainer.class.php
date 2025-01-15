<?php

namespace wcf\system\form\builder\container\wysiwyg;

use wcf\system\form\builder\container\TabFormContainer;
use wcf\system\form\builder\TWysiwygFormNode;

/**
 * Represents a container that is a tab of a wysiwyg tab menu.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class WysiwygTabFormContainer extends TabFormContainer implements IWysiwygTabFormContainer
{
    use TWysiwygFormNode;

    /**
     * @inheritDoc
     */
    protected $templateName = 'shared_wysiwygTabFormContainer';

    protected ?string $icon = null;
    protected string $name = '';

    #[\Override]
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * Sets the icon associated with the tab.
     */
    public function icon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    #[\Override]
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name associated with the tab.
     */
    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
