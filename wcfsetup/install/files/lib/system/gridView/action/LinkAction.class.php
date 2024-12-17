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
        protected readonly string $controllerClass,
        protected readonly string|Closure $languageItem,
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

        if (\is_string($this->languageItem)) {
            $title = WCF::getLanguage()->get($this->languageItem);
        } else {
            $title = ($this->languageItem)($row);
        }

        return \sprintf('<a href="%s">%s</a>', StringUtil::encodeHTML($href), $title);
    }
}
