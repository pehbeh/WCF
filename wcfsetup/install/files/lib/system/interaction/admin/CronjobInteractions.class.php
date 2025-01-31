<?php

namespace wcf\system\interaction\admin;

use wcf\data\cronjob\Cronjob;
use wcf\event\interaction\admin\CronjobInteractionCollecting;
use wcf\system\event\EventHandler;
use wcf\system\interaction\AbstractInteractionProvider;
use wcf\system\interaction\DeleteInteraction;

/**
 * Interaction provider for cronjobs.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class CronjobInteractions extends AbstractInteractionProvider
{
    public function __construct()
    {
        $this->addInteractions([
            new DeleteInteraction('core/cronjobs/%s', static fn(Cronjob $cronjob) => $cronjob->isDeletable()),
        ]);

        EventHandler::getInstance()->fire(
            new CronjobInteractionCollecting($this)
        );
    }

    #[\Override]
    public function getObjectClassName(): string
    {
        return Cronjob::class;
    }
}
