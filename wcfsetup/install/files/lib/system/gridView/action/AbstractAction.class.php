<?php

namespace wcf\system\gridView\action;

use Closure;

/**
 * Provides an abstract implementation of a grid view action.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
abstract class AbstractAction implements IGridViewAction
{
    public function __construct(
        private readonly ?Closure $isAvailableCallback = null
    ) {}

    #[\Override]
    public function isAvailable(mixed $row): bool
    {
        if ($this->isAvailableCallback === null) {
            return true;
        }

        return ($this->isAvailableCallback)($row);
    }

    #[\Override]
    public function isQuickAction(): bool
    {
        return false;
    }
}
