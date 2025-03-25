<?php

namespace wcf\system\cache\eager\data;

use wcf\data\category\Category;

/**
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
final class CategoryCacheData
{
    public function __construct(
        /** @var Category[] */
        public readonly array $categories,
        /** @var array<int, list<int>> */
        public readonly array $objectTypeCategoryIDs
    ) {
    }

    public function getCategory(int $categoryID): ?Category
    {
        return $this->categories[$categoryID] ?? null;
    }

    /**
     * @return array<int, Category>
     */
    public function getCategoriesForObjectType(string $objectType): array
    {
        $categories = [];
        foreach ($this->getCategoryIDsForObjectType($objectType) as $categoryID) {
            $categories[$categoryID] = $this->getCategory($categoryID);
        }

        return $categories;
    }

    /**
     * @return list<int>
     */
    public function getCategoryIDsForObjectType(string $objectType): array
    {
        return $this->objectTypeCategoryIDs[$objectType] ?? [];
    }
}
