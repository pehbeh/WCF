<?php

namespace wcf\system\moderation;

use wcf\event\moderation\DeletedItemsCollecting;
use wcf\system\event\EventHandler;
use wcf\system\WCF;

/**
 * Represents the deleted items box.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class DeletedItemsBoxComponent
{
    public function __construct(
        public readonly string $activeId,
    ) {}

    public function render(): string
    {
        return WCF::getTPL()->render(
            'wcf',
            'deletedItemsBox',
            [
                'types' => $this->getTypes(),
                'activeId' => $this->activeId,
            ],
        );
    }

    /**
     * @return list<DeletedItems>
     */
    private function getTypes(): array
    {
        $event = new DeletedItemsCollecting();
        EventHandler::getInstance()->fire($event);

        return $event->getTypes();
    }
}
