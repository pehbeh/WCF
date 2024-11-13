<?php

namespace wcf\system\gridView\action;

use Closure;
use wcf\data\DatabaseObject;
use wcf\system\gridView\AbstractGridView;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Represents an edit action.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class EditAction extends AbstractAction
{
    public function __construct(
        private readonly string $controllerClass,
        ?Closure $isAvailableCallback = null
    ) {
        parent::__construct($isAvailableCallback);
    }

    #[\Override]
    public function render(mixed $row): string
    {
        \assert($row instanceof DatabaseObject);
        $href = LinkHandler::getInstance()->getControllerLink(
            $this->controllerClass,
            ['object' => $row]
        );

        return '<a href="' . $href . '">' . WCF::getLanguage()->get('wcf.global.button.edit') . '</a>';
    }

    #[\Override]
    public function renderInitialization(AbstractGridView $gridView): ?string
    {
        return null;
    }
}
