<?php

namespace wcf\system\interaction;

use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
 * Represents the component of a standalone button for an interaction content menu.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class StandaloneInteractionContextMenuComponent extends InteractionContextMenuComponent
{
    public function __construct(
        IInteractionProvider $provider,
        protected readonly DatabaseObject $object,
        protected readonly string $redirectUrl,
        protected readonly string $label = '',
        protected readonly string $icon = 'ellipsis-vertical',
        protected readonly string $cssClassName = '',
        protected readonly string $buttonCssClassName = ''
    ) {
        parent::__construct($provider);
    }

    public function render(): string
    {
        $contextMenuOptions = $this->renderContextMenuOptions($this->object);
        if (!$contextMenuOptions) {
            return '';
        }

        return WCF::getTPL()->render(
            'wcf',
            'shared_standaloneInteractionButton',
            [
                'contextMenuOptions' => $contextMenuOptions,
                'initializationCode' => $this->renderInitialization($this->getContainerID()),
                'containerID' => $this->getContainerID(),
                'providerClassName' => \get_class($this->provider),
                'objectID' => $this->object->getObjectID(),
                'redirectUrl' => $this->redirectUrl,
                'label' => $this->label,
                'icon' => $this->icon,
                'cssClassName' => $this->cssClassName,
                'buttonCssClassName' => $this->buttonCssClassName,
            ],
        );
    }

    public function getContainerID(): string
    {
        $classNamePieces = \explode('\\', \get_class($this->object));

        return \implode('-', $classNamePieces) . '-' . $this->object->getObjectID();
    }

    public static function forContentHeaderButton(
        IInteractionProvider $provider,
        DatabaseObject $object,
        string $redirectUrl,
    ): self {
        return new self($provider, $object, $redirectUrl, icon: 'ellipsis-vertical');
    }

    public static function forContentInteractionButton(
        IInteractionProvider $provider,
        DatabaseObject $object,
        string $redirectUrl,
        string $label,
    ): self {
        return new self($provider, $object, $redirectUrl, $label, 'pencil', 'contentInteractionButton', 'small');
    }
}
