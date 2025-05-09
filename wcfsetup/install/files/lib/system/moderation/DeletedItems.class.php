<?php

namespace wcf\system\moderation;

/**
 * Represents a type of deleted items.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class DeletedItems
{
    public function __construct(
        public readonly string $id,
        public readonly string $languageItem,
        public readonly string $link
    ) {}
}
