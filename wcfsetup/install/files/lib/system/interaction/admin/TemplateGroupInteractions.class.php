<?php

namespace wcf\system\interaction\admin;

use wcf\data\template\group\TemplateGroup;
use wcf\event\interaction\admin\TemplateGroupInteractionCollecting;
use wcf\system\event\EventHandler;
use wcf\system\interaction\AbstractInteractionProvider;
use wcf\system\interaction\DeleteInteraction;

/**
 * Interaction provider for template groups.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class TemplateGroupInteractions extends AbstractInteractionProvider
{
    public function __construct()
    {
        $this->addInteractions([
            new DeleteInteraction("core/templates/groups/%s", static fn(TemplateGroup $group) => !$group->isImmutable())
        ]);

        EventHandler::getInstance()->fire(
            new TemplateGroupInteractionCollecting($this)
        );
    }

    #[\Override]
    public function getObjectClassName(): string
    {
        return TemplateGroup::class;
    }
}
