<?php

namespace wcf\system\showOrder;

/**
 * Represents an element that is used to change the show order.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class ShowOrderItem
{
    public function __construct(
        public readonly int $id,
        public readonly string $label,
    ) {}
}
