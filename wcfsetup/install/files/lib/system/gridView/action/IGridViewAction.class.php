<?php

namespace wcf\system\gridView\action;

use wcf\system\gridView\AbstractGridView;

/**
 * Represents an action of a grid view.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
interface IGridViewAction
{
    /**
     * Renders the action.
     */
    public function render(mixed $row): string;

    /**
     * Renders the initialization code for this action.
     */
    public function renderInitialization(AbstractGridView $gridView): ?string;

    /**
     * Returns true if this is a quick action.
     */
    public function isQuickAction(): bool;

    /**
     * Returns true if this action is available for the given row.
     */
    public function isAvailable(mixed $row): bool;
}
