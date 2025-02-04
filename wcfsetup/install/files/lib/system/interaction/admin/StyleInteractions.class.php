<?php

namespace wcf\system\interaction\admin;

use wcf\data\style\Style;
use wcf\event\interaction\admin\StyleInteractionCollecting;
use wcf\system\event\EventHandler;
use wcf\system\interaction\AbstractInteractionProvider;
use wcf\system\interaction\DeleteInteraction;
use wcf\system\interaction\RpcInteraction;

/**
 * Interaction provider for user ranks.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class StyleInteractions extends AbstractInteractionProvider
{
    public function __construct()
    {
        $this->addInteractions([
            new DeleteInteraction('core/styles/%s', static fn(Style $object) => !$object->isDefault),
            new RpcInteraction(
                'set-as-default',
                'core/styles/%s/set-as-default',
                'wcf.acp.style.button.setAsDefault',
                isAvailableCallback: static fn(Style $object) => !$object->isDefault
            ),
        ]);

        EventHandler::getInstance()->fire(
            new StyleInteractionCollecting($this)
        );
    }

    #[\Override]
    public function getObjectClassName(): string
    {
        return Style::class;
    }
}
