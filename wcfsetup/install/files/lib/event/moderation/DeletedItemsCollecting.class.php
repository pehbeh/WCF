<?php

namespace wcf\event\moderation;

use wcf\data\object\type\ObjectTypeCache;
use wcf\event\IPsr14Event;
use wcf\page\DeletedContentListPage;
use wcf\system\moderation\DeletedItems;
use wcf\system\request\LinkHandler;

/**
 * Requests the collection of deleted item types.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class DeletedItemsCollecting implements IPsr14Event
{
    /**
     * @var DeletedItems[]
     */
    private array $types = [];

    public function __construct()
    {
        $this->loadLegacyProviders();
    }

    public function register(DeletedItems $type): void
    {
        $this->types[] = $type;
    }

    /**
     * @return DeletedItems[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @deprecated 6.2
     */
    private function loadLegacyProviders(): void
    {
        $objectTypes = ObjectTypeCache::getInstance()->getObjectTypes('com.woltlab.wcf.deletedContent');
        foreach ($objectTypes as $objectType) {
            $this->register(new DeletedItems(
                $objectType->objectType,
                'wcf.moderation.deletedContent.objectType.' . $objectType->objectType,
                LinkHandler::getInstance()->getControllerLink(
                    DeletedContentListPage::class,
                    ['objectType' => $objectType->objectType]
                )
            ));
        }
    }
}
