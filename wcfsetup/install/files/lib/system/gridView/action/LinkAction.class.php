<?php

namespace wcf\system\gridView\action;

use Closure;
use wcf\data\DatabaseObject;
use wcf\system\gridView\AbstractGridView;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents an action that links to a given controller.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class LinkAction extends AbstractAction
{
    public function __construct(
        private readonly string $controllerClass,
        private readonly string $languageItem,
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

        return '<a href="' . StringUtil::encodeHTML($href) . '">' . WCF::getLanguage()->get($this->languageItem) . '</a>';
    }

    #[\Override]
    public function renderInitialization(AbstractGridView $gridView): ?string
    {
        return null;
    }
}
