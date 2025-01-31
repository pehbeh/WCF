<?php

namespace wcf\system\interaction\admin;

use wcf\data\bbcode\BBCode;
use wcf\event\interaction\admin\BBCodeInteractionCollecting;
use wcf\system\event\EventHandler;
use wcf\system\interaction\AbstractInteractionProvider;
use wcf\system\interaction\DeleteInteraction;

/**
 * Interaction provider for bb codes.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class BBCodeInteractions extends AbstractInteractionProvider
{
    public function __construct()
    {
        $this->addInteractions([
            new DeleteInteraction('core/bb-codes/%s', static fn(BBCode $bbcode) => $bbcode->canDelete()),
        ]);

        EventHandler::getInstance()->fire(
            new BBCodeInteractionCollecting($this)
        );
    }

    #[\Override]
    public function getObjectClassName(): string
    {
        return BBCode::class;
    }
}
