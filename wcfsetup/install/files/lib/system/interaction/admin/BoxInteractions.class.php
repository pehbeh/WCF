<?php

namespace wcf\system\interaction\admin;

use wcf\data\box\Box;
use wcf\event\interaction\admin\BoxInteractionCollecting;
use wcf\system\event\EventHandler;
use wcf\system\interaction\AbstractInteractionProvider;
use wcf\system\interaction\DeleteInteraction;

/**
 * Interaction provider for boxes.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class BoxInteractions extends AbstractInteractionProvider
{
    public function __construct()
    {
        $this->addInteractions([
            new DeleteInteraction('core/boxes/%s', static fn(Box $object) => $object->canDelete())
        ]);

        EventHandler::getInstance()->fire(
            new BoxInteractionCollecting($this)
        );
    }

    #[\Override]
    public function getObjectClassName(): string
    {
        return Box::class;
    }
}
