<?php

namespace wcf\system\gridView\renderer;

use wcf\data\DatabaseObject;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Allows the setting of a link to a column.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class LinkColumnRenderer extends AbstractColumnRenderer implements ILinkColumnRenderer
{
    public function __construct(
        private readonly string $controllerClass,
        private readonly array $parameters = [],
        private readonly string $titleLanguageItem = ''
    ) {}

    #[\Override]
    public function render(mixed $value, mixed $context = null): string
    {
        \assert($context instanceof DatabaseObject);
        $href = LinkHandler::getInstance()->getControllerLink(
            $this->controllerClass,
            \array_merge($this->parameters, ['object' => $context])
        );

        return '<a href="' . StringUtil::encodeHTML($href) . '"'
            . ($this->titleLanguageItem ? ' title="' . WCF::getLanguage()->get($this->titleLanguageItem) . '"' : '') . '>'
            . $value
            . '</a>';
    }
}
