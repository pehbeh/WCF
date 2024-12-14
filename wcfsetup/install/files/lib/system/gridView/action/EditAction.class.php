<?php

namespace wcf\system\gridView\action;

use Closure;

/**
 * Represents an edit action.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class EditAction extends LinkAction
{
    public function __construct(
        string $controllerClass,
        ?Closure $isAvailableCallback = null
    ) {
        parent::__construct($controllerClass, 'wcf.global.button.edit', $isAvailableCallback);
    }
}
