<?php

namespace wcf\system\interaction\admin;

use wcf\data\label\group\LabelGroup;
use wcf\event\interaction\admin\LabelGroupInteractionCollecting;
use wcf\system\event\EventHandler;
use wcf\system\interaction\AbstractInteractionProvider;
use wcf\system\interaction\DeleteInteraction;

/**
 * Interaction provider for label groups.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class LabelGroupInteractions extends AbstractInteractionProvider
{
    public function __construct()
    {
        $this->addInteractions([
            new DeleteInteraction('core/labels/groups/%s')
        ]);

        EventHandler::getInstance()->fire(
            new LabelGroupInteractionCollecting($this)
        );
    }

    #[\Override]
    public function getObjectClassName(): string
    {
        return LabelGroup::class;
    }
}
